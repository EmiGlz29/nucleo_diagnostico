<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pacientes = Paciente::all();
        return view('PACIENTES.MostrarPaciente', compact('pacientes'));
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
