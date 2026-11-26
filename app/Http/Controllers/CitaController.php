<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\EMPLEADO;
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
    private const MIN_APPOINTMENT_DATE = '2026-11-23';

    private const SQL_AGENDA = 'SELECT c.codigocita, c.codigopaciente, c.codigodoctor, c.fechacita, c.hora, p.nombre AS paciente FROM "Cita" c JOIN "Paciente" p ON p.codigo = c.codigopaciente WHERE c.codigodoctor = ? AND c.fechacita BETWEEN ? AND ? ORDER BY c.fechacita, c.hora';

    public function index(): View
    {
        $citas = Cita::with(['paciente', 'doctor', 'empleado'])->orderBy('fechacita', 'desc')->get();

        return view('CITAS.MostrarCitas', compact('citas'));
    }

    public function create(): View
    {
        return $this->form();
    }

    public function edit(string $codigocita): View
    {
        return view('CITAS.ModificarCitas', [
            'cita' => Cita::query()->findOrFail($codigocita),
            'doctores' => Doctor::query()->orderBy('nombre')->get(['codigo', 'nombre']),
        ]);
    }

    public function destroy(string $codigocita): RedirectResponse
{
    DB::delete('DELETE FROM "cita" WHERE codigocita = ?', [$codigocita]);

    return redirect()->route('citas.create')->with('success', 'La cita se eliminó correctamente.');
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
        abort_unless($request->user() instanceof EMPLEADO, 403, 'Solo los empleados pueden registrar citas.');

        return $this->save($request);
    }

    public function update(Request $request, string $codigocita): RedirectResponse
    {
        $current = Cita::query()->findOrFail($codigocita);

        return $this->save($request, $current);
    }

    private function form(?Cita $cita = null): View
    {
        $maxCode = (int) DB::table('Cita')->max('codigocita');

        return view('CITAS.Agenda', [
            'doctores' => Doctor::query()->orderBy('nombre')->get(['codigo', 'nombre', 'especialidad']),
            'pacientes' => Paciente::query()->orderBy('nombre')->get(['codigo', 'nombre']),
            'cita' => $cita,
            'nextCode' => $maxCode < 2147483647 ? $maxCode + 1 : null,
            'minAppointmentDate' => self::MIN_APPOINTMENT_DATE,
        ]);
    }

    private function save(Request $request, ?Cita $current = null): RedirectResponse
    {
        $statement = $current
            ? 'UPDATE "Cita" SET codigocita = ?, codigopaciente = ?, codigodoctor = ?, fechacita = ?, hora = ? WHERE codigocita = ?'
            : 'INSERT INTO "Cita" (codigocita, codigopaciente, codigodoctor, fechacita, hora, codigoempleado) VALUES (?, ?, ?, ?, ?, ?)';
        $codeRule = Rule::unique('Cita', 'codigocita');

        if ($current) {
            $codeRule->ignore($current->codigocita, 'codigocita');
        }

        $validator = Validator::make($request->all(), [
            'codigocita' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'max:2147483647',
                $codeRule,
            ],
            'codigopaciente' => ['bail', 'required', 'integer', Rule::exists('Paciente', 'codigo')],
            'codigodoctor' => ['bail', 'required', 'integer', Rule::exists('Doctor', 'codigo')],
            'fechacita' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', Rule::in(array_map(fn (int $hour): string => sprintf('%02d:00', $hour), range(9, 19)))],
        ], [
            'fechacita.date_format' => 'Seleccione una fecha válida.',
            'hora.in' => 'Seleccione un horario válido de atención.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $date = CarbonImmutable::parse($data['fechacita']);

        if ($date->toDateString() < self::MIN_APPOINTMENT_DATE) {
            return back()->withErrors([
                'fechacita' => 'No se pueden agendar ni reprogramar citas antes del 23 de noviembre de 2026.',
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

        $bindings = $this->writeBindings($data, $current, $request);

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

    private function findConflict(array $data, int|string|null $currentCode): ?array
    {
        $selfExclusion = $currentCode === null ? '' : ' AND c.codigocita <> ?';
        $suffixBindings = $currentCode === null ? [] : [$currentCode];
        $doctorSql = 'SELECT c.codigocita FROM "Cita" c WHERE c.codigodoctor = ? AND c.fechacita = ? AND c.hora = ?'.$selfExclusion;
        $doctorBindings = [(int) $data['codigodoctor'], $data['fechacita'], $data['hora'], ...$suffixBindings];

        if (DB::selectOne($doctorSql, $doctorBindings)) {
            return [
                'field' => 'hora',
                'message' => 'No se puede registrar la cita: el doctor ya tiene una cita registrada en ese horario',
            ];
        }

        $patientSql = 'SELECT c.codigocita FROM "Cita" c WHERE c.codigopaciente = ? AND c.fechacita = ? AND c.hora = ?'.$selfExclusion;
        $patientBindings = [
            (int) $data['codigopaciente'],
            $data['fechacita'],
            $data['hora'],
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

    private function writeBindings(array $data, ?Cita $current, Request $request): array
    {
        $values = [
            (int) $data['codigocita'],
            (int) $data['codigopaciente'],
            (int) $data['codigodoctor'],
            $data['fechacita'],
            $data['hora'],
        ];

        return $current ? [...$values, $current->codigocita] : [...$values, $request->user()->getAuthIdentifier()];
    }
}
