<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Models\Cita;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $doctores = Doctor::all();

        return view('DOCTORES.MostrarDoctor', compact('doctores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('DOCTORES.InsertarDoctor');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $doctor = Doctor::create($validated);

        return redirect()->route('doctores.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor): View
    {
        return view('DOCTORES.MostrarDoctor', ['doctor' => $doctor]);
    }

    /**
     * Display the weekly agenda of the specified doctor.
     */
    public function agenda(Doctor $doctor, Request $request): View
    {
        $validated = $request->validate([
            'semana' => ['nullable', 'date'],
        ]);

        $lunes = isset($validated['semana'])
            ? Carbon::parse($validated['semana'])->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $dias = collect(config('agenda.dias', [1, 2, 3, 4, 5]))
            ->map(fn (int $dia) => $lunes->copy()->addDays($dia - 1));

        [$comidaInicio, $comidaFin] = config('agenda.comida', [14, 15]);

        $horas = collect(range(config('agenda.hora_inicio', 9), config('agenda.hora_fin', 19)))
            ->map(fn (int $hora) => [
                'inicio' => sprintf('%02d:00', $hora),
                'fin' => sprintf('%02d:00', $hora + 1),
                'comida' => $hora >= $comidaInicio && $hora <= $comidaFin,
            ]);

        $citas = Cita::where('codigodoctor', $doctor->codigo)
            ->whereBetween('fechacita', [$dias->first()->toDateString(), $dias->last()->toDateString()])
            ->get()
            ->keyBy(fn (Cita $cita) => Carbon::parse($cita->fechacita)->toDateString().'|'.substr($cita->hora, 0, 5));

        return view('DOCTORES.AgendaDoctor', [
            'doctor' => $doctor,
            'dias' => $dias,
            'horas' => $horas,
            'citas' => $citas,
            'semanaAnterior' => $lunes->copy()->subWeek()->toDateString(),
            'semanaSiguiente' => $lunes->copy()->addWeek()->toDateString(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor): void
    {
        //
    }
}
