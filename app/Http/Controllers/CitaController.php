<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $citas = Cita::all();
        return view('CITAS.MostrarCitas', compact('citas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('CITAS.CrearCita');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validated();
        $cita = Cita::create($validated);
        return redirect()->route('citas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cita $cita): View
    {
        return view('CITAS.MostrarCitas', compact('cita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cita $cita): View
    {
        return view('CITAS.EditarCita', compact('cita'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $validated = $request->validated();
        $cita->update($validated);
        return redirect()->route('citas.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cita $cita): RedirectResponse
    {
        $cita->delete();
        return redirect()->route('citas.index');
    }
}
