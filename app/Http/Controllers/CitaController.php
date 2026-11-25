<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CitaController extends Controller
{
    private const SQL_AGENDA = 'SELECT c.codigocita, c.codigopaciente, c.codigodoctor, c.fechacita, c.hora, p.nombre AS paciente FROM "cita" c JOIN "Paciente" p ON p.codigo = c.codigopaciente WHERE c.codigodoctor = ? AND c.fechacita BETWEEN ? AND ? ORDER BY c.fechacita, c.hora';

    public function create(): View
    {
        return $this->form();
    }

    public function edit(string $codigocita): View
    {
        return $this->form(Cita::query()->findOrFail($codigocita));
    }

    public function agenda(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigodoctor' => ['required', 'integer', Rule::exists('Doctor', 'codigo')],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:fecha'],
        ]);

        $start = CarbonImmutable::parse($validated['fecha']);
        $end = CarbonImmutable::parse($validated['hasta'] ?? $validated['fecha']);

        if ($start->diffInDays($end) > 41) {
            return response()->json(['message' => 'El periodo de consulta no puede superar 42 días.'], 422);
        }

        $bindings = [
            (int) $validated['codigodoctor'],
            $start->toDateString(),
            $end->toDateString(),
        ];
        $citas = DB::select(self::SQL_AGENDA, $bindings);

        return response()->json([
            'citas' => $citas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request);
    }

    public function update(Request $request, string $codigocita): RedirectResponse
    {
        $current = Cita::query()->findOrFail($codigocita);

        return $this->save($request, $current);
    }

    private function form(?Cita $cita = null): View
    {
        $citas = DB::select('SELECT codigocita FROM "cita" WHERE codigocita LIKE ?', ['CIT-%']);
        $nextNumber = collect($citas)
            ->map(fn (object $row): int => (int) substr($row->codigocita, 4))
            ->max() + 1;

        return view('CITAS.Agenda', [
            'doctores' => Doctor::query()->orderBy('nombre')->get(['codigo', 'nombre', 'especialidad']),
            'pacientes' => Paciente::query()->orderBy('nombre')->get(['codigo', 'nombre']),
            'cita' => $cita,
            'nextCode' => sprintf('CIT-%03d', $nextNumber),
        ]);
    }

    private function save(Request $request, ?Cita $current = null): RedirectResponse
    {
        $statement = $current
            ? 'UPDATE "cita" SET codigocita = ?, codigopaciente = ?, codigodoctor = ?, fechacita = ?, hora = ? WHERE codigocita = ?'
            : 'INSERT INTO "cita" (codigocita, codigopaciente, codigodoctor, fechacita, hora) VALUES (?, ?, ?, ?, ?)';
        $codeRule = Rule::unique('cita', 'codigocita');

        if ($current) {
            $codeRule->ignore($current->codigocita, 'codigocita');
        }

        $validator = Validator::make($request->all(), [
            'codigocita' => [
                'required',
                'string',
                'max:20',
                'regex:/^CIT-\d{3,}$/',
                $codeRule,
            ],
            'codigopaciente' => ['required', 'integer', Rule::exists('Paciente', 'codigo')],
            'codigodoctor' => ['required', 'integer', Rule::exists('Doctor', 'codigo')],
            'fechacita' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', Rule::in(array_map(fn (int $hour): string => sprintf('%02d:00', $hour), range(9, 19)))],
        ], [
            'codigocita.regex' => 'El código de cita debe tener el formato CIT-001.',
            'fechacita.date_format' => 'Seleccione una fecha válida.',
            'hora.in' => 'Seleccione un horario válido de atención.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $date = CarbonImmutable::parse($data['fechacita']);

        if ($date->isPast() && $date->toDateString() < today()->toDateString()) {
            return back()->withErrors([
                'fechacita' => 'No se pueden agendar ni reprogramar citas en fechas pasadas.',
            ])->withInput();
        }

        if ($date->isWeekend()) {
            return back()->withErrors(['fechacita' => 'El doctor solo atiende de lunes a viernes.'])
                ->withInput();
        }

        if ($data['hora'] >= '14:00' && $data['hora'] < '16:00') {
            return back()->withErrors([
                'hora' => 'No se puede registrar la cita: horario de comida del doctor (2:00 PM a 4:00 PM)',
            ])->withInput();
        }

        $conflict = $this->findConflict($data, $current?->codigocita);

        if ($conflict !== null) {
            return back()->withErrors([$conflict['field'] => $conflict['message']])
                ->withInput();
        }

        $bindings = $this->writeBindings($data, $current);

        try {
            DB::transaction(function () use ($statement, $bindings, $current): void {
                if ($current) {
                    DB::update($statement, $bindings);
                } else {
                    DB::insert($statement, $bindings);
                }
            });
        } catch (QueryException $exception) {
            if (! in_array($exception->errorInfo[0] ?? null, ['23505', '23000'], true)) {
                throw $exception;
            }

            $conflict = $this->findConflict($data, $current?->codigocita);

            if ($conflict === null) {
                return back()->withErrors([
                    'codigocita' => 'El código de cita ya fue utilizado por otra solicitud.',
                ])->withInput();
            }

            return back()->withErrors([$conflict['field'] => $conflict['message']])
                ->withInput();
        }

        $message = $current ? 'La cita se reprogramó correctamente.' : 'La cita se registró correctamente.';

        return redirect()->route('citas.create')->with('success', $message);
    }

    private function findConflict(array $data, ?string $currentCode): ?array
    {
        $selfExclusion = $currentCode === null ? '' : ' AND c.codigocita <> ?';
        $suffixBindings = $currentCode === null ? [] : [$currentCode];
        $doctorSql = 'SELECT c.codigocita FROM "cita" c WHERE c.codigodoctor = ? AND c.fechacita = ? AND c.hora = ?'.$selfExclusion;
        $doctorBindings = [(int) $data['codigodoctor'], $data['fechacita'], $data['hora'], ...$suffixBindings];

        if (DB::selectOne($doctorSql, $doctorBindings)) {
            return [
                'field' => 'hora',
                'message' => 'No se puede registrar la cita: el doctor ya tiene una cita registrada en ese horario',
            ];
        }

        $patientSql = 'SELECT c.codigocita FROM "cita" c WHERE c.codigopaciente = ? AND c.fechacita = ? AND c.hora = ? AND c.codigodoctor <> ?'.$selfExclusion;
        $patientBindings = [
            (int) $data['codigopaciente'],
            $data['fechacita'],
            $data['hora'],
            (int) $data['codigodoctor'],
            ...$suffixBindings,
        ];

        if (DB::selectOne($patientSql, $patientBindings)) {
            return [
                'field' => 'codigopaciente',
                'message' => 'No se puede registrar la cita: el paciente ya tiene otra cita agendada en esa misma fecha y hora con otro doctor',
            ];
        }

        return null;
    }

    private function writeBindings(array $data, ?Cita $current): array
    {
        $values = [
            $data['codigocita'] ?? '',
            (int) ($data['codigopaciente'] ?? 0),
            (int) ($data['codigodoctor'] ?? 0),
            $data['fechacita'] ?? '',
            $data['hora'] ?? '',
        ];

        return $current ? [...$values, $current->codigocita] : $values;
    }
}
