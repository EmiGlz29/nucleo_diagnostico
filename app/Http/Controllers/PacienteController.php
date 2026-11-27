<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $proximaCita = null;
        $user = $request->user();

        if ($user instanceof Doctor) {
            $now = CarbonImmutable::now();
            $proximaCita = Cita::query()
                ->with('paciente')
                ->where('codigodoctor', $user->codigo)
                ->where(function ($query) use ($now): void {
                    $query->whereDate('fechacita', '>', $now->toDateString())
                        ->orWhere(function ($today) use ($now): void {
                            $today->whereDate('fechacita', $now->toDateString())
                                ->whereTime('hora', '>=', $now->format('H:i:s'));
                        });
                })
                ->orderBy('fechacita')
                ->orderBy('hora')
                ->first();

        }

        $pacientes = Paciente::query()->orderBy('nombre')->get();

        return view('PACIENTES.MostrarPaciente', compact('pacientes', 'proximaCita'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('PACIENTES.InsertarPaciente');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePacienteRequest $request)
    {
        $validated = $request->validated();

        // Crear el empleado (contraseña sin hashear)
        $paciente = Paciente::create($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('menu-principal')
            ->with('success', 'Paciente '.$paciente->nombre.' agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paciente $paciente)
    {
        return view('Pacientes.show', ['paciente' => $paciente]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePacienteRequest $request, Paciente $paciente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paciente $paciente)
    {
        //
    }
}
