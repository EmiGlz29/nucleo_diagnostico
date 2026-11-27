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

    public function index(Request $request): View
    {
        $query = Cita::query()->with(['paciente', 'doctor']);

        if ($request->user() instanceof Doctor) {
            $query->where('codigodoctor', $request->user()->getAuthIdentifier());
        }

        $citas = $query->orderByDesc('fechacita')->orderBy('hora')->get();

        return view('CITAS.MostrarCitas', compact('citas'));
    }

    public function create(): View
    {
        return $this->form();
    }

    public function edit(string $codigocita, string $codigopaciente): View
    {
        return view('CITAS.ModificarCitas', [
            'cita' => $this->findCita($codigocita, $codigopaciente),
            'doctores' => Doctor::query()->orderBy('nombre')->get(['codigo', 'nombre']),
        ]);
    }

    public function destroy(string $codigocita, string $codigopaciente): RedirectResponse
    {
        $deleted = DB::delete(
            'DELETE FROM "Cita" WHERE codigocita = ? AND codigopaciente = ?',
            [$codigocita, $codigopaciente],
        );

        abort_unless($deleted === 1, 404);

        return redirect()->route('citas.index')->with('success', 'La cita se eliminó correctamente.');
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

        $citas = DB::select(self::SQL_AGENDA, [
            (int) $validated['codigodoctor'],
            $start->toDateString(),
            $end->toDateString(),
        ]);

        return response()->json(['citas' => $citas]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(
            $request->user() instanceof EMPLEADO,
            403,
            'Solo los empleados pueden registrar citas.',
        );

        return $this->save($request);
    }

    public function update(Request $request, string $codigocita, string $codigopaciente): RedirectResponse
    {
        return $this->save($request, $this->findCita($codigocita, $codigopaciente));
    }

    private function form(): View
    {
        $maxCode = (int) DB::table('Cita')->max('codigocita');

        return view('CITAS.Agenda', [
            'doctores' => Doctor::query()->orderBy('nombre')->get(['codigo', 'nombre', 'especialidad']),
            'pacientes' => Paciente::query()->orderBy('nombre')->get(['codigo', 'nombre']),
            'cita' => null,
            'nextCode' => $maxCode < 2147483647 ? $maxCode + 1 : null,
            'minAppointmentDate' => self::MIN_APPOINTMENT_DATE,
        ]);
    }

    private function findCita(string|int $codigocita, string|int $codigopaciente): Cita
    {
        return Cita::query()
            ->with(['paciente', 'doctor'])
            ->where('codigocita', $codigocita)
            ->where('codigopaciente', $codigopaciente)
            ->firstOrFail();
    }

    private function save(Request $request, ?Cita $current = null): RedirectResponse
    {
        $input = $request->all();
        foreach (['codigocita', 'codigopaciente', 'codigodoctor', 'fechacita', 'hora'] as $field) {
            if (is_string($input[$field] ?? null)) {
                $input[$field] = trim($input[$field]);
            }
        }

        $validator = Validator::make($input, [
            'codigocita' => ['bail', 'required', 'integer', 'min:1', 'max:2147483647'],
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
        $sameCompositeKey = $current !== null
            && (string) $current->codigocita === (string) $data['codigocita']
            && (string) $current->codigopaciente === (string) $data['codigopaciente'];

        if (! $sameCompositeKey && DB::table('Cita')
            ->where('codigocita', $data['codigocita'])
            ->where('codigopaciente', $data['codigopaciente'])
            ->exists()) {
            return back()->withErrors([
                'codigocita' => 'Ya existe una cita con esa combinación de código y paciente.',
            ])->withInput();
        }

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
                'hora' => 'No se puede registrar la cita durante el horario de comida (14:00 a 16:00).',
            ])->withInput();
        }

        $conflict = $this->findConflict($data, $current);
        if ($conflict !== null) {
            return back()->withErrors([$conflict['field'] => $conflict['message']])->withInput();
        }

        $statement = $current
            ? 'UPDATE "Cita" SET codigocita = ?, codigopaciente = ?, codigodoctor = ?, fechacita = ?, hora = ? WHERE codigocita = ? AND codigopaciente = ?'
            : 'INSERT INTO "Cita" (codigocita, codigopaciente, codigodoctor, fechacita, hora, codigoempleado) VALUES (?, ?, ?, ?, ?, ?)';
        $values = [
            (int) $data['codigocita'],
            (int) $data['codigopaciente'],
            (int) $data['codigodoctor'],
            $data['fechacita'],
            $data['hora'],
        ];
        $bindings = $current
            ? [...$values, $current->codigocita, $current->codigopaciente]
            : [...$values, $request->user()->getAuthIdentifier()];

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

            return back()->withErrors([
                'codigocita' => 'Ya existe una cita con esa combinación de código y paciente.',
            ])->withInput();
        }

        $message = $current ? 'La cita se reprogramó correctamente.' : 'La cita se registró correctamente.';

        return redirect()->route('citas.create')->with('success', $message);
    }

    private function findConflict(array $data, ?Cita $current): ?array
    {
        $selfExclusion = $current === null ? '' : ' AND NOT (c.codigocita = ? AND c.codigopaciente = ?)';
        $suffixBindings = $current === null ? [] : [$current->codigocita, $current->codigopaciente];
        $doctorSql = 'SELECT c.codigocita FROM "Cita" c WHERE c.codigodoctor = ? AND c.fechacita = ? AND c.hora = ?'.$selfExclusion;
        $doctorBindings = [(int) $data['codigodoctor'], $data['fechacita'], $data['hora'], ...$suffixBindings];

        if (DB::selectOne($doctorSql, $doctorBindings)) {
            return [
                'field' => 'hora',
                'message' => 'El doctor ya tiene una cita registrada en ese horario.',
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
                'message' => 'El paciente ya tiene una cita agendada en esa fecha y hora.',
            ];
        }

        return null;
    }
}
