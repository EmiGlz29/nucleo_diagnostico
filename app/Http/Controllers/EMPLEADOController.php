<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEMPLEADORequest;
use App\Http\Requests\UpdateEMPLEADORequest;
use App\Models\EMPLEADO;

class EMPLEADOController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empleados = EMPLEADO::all();

        return view('EMPLEADOS.MostrarEmpleado', compact('empleados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('EMPLEADOS.InsertarEmpleado');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEMPLEADORequest $request)
    {
        // Validar con el FormRequest
        $validated = $request->validated();

        // Crear el empleado (contraseña sin hashear)
        $empleado = EMPLEADO::create($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('menu-principal')
            ->with('success', 'Empleado '.$empleado->nombre.' agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EMPLEADO $eMPLEADO)
    {
        return view('EMPLEADOS.MostrarEmpleado', ['empleado' => $eMPLEADO]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EMPLEADO $eMPLEADO)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEMPLEADORequest $request, EMPLEADO $eMPLEADO)
    {
        //
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(EMPLEADO $eMPLEADO)
    {
        //
    }
}
