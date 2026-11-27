<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreMedicamentoRequest;
use App\Http\Requests\UpdateMedicamentoRequest;

class MedicamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicamentos = Medicamento::all();
        return view('MEDICAMENTOS.MostrarMedicamento', compact('medicamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('MEDICAMENTOS.InsertarMedicamento');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicamentoRequest $request)
    {
        $validated = $request->validated();
        $medicamento = Medicamento::create($validated);
        return redirect()->route('medicamentos.index');
        return redirect()->route('menu-principal')
            ->with('success', 'Medicamento '.$medicamento->nombre_med.' agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicamento $medicamento)
    {
        return view('MEDICAMENTOS.MostrarMedicamento', ['medicamento' => $medicamento]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicamento $medicamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicamentoRequest $request, Medicamento $medicamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicamento $medicamento)
    {
        //
    }
}
