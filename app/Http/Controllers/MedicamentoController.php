<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicamentoRequest;
use App\Http\Requests\UpdateMedicamentoRequest;
use App\Models\Medicamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MedicamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $medicamentos = Medicamento::query()->orderBy('nombre_med')->get();

        return view('MEDICAMENTOS.MostrarMedicamento', compact('medicamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('MEDICAMENTOS.InsertarMedicamento');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicamentoRequest $request): RedirectResponse
    {
        $medicamento = Medicamento::create($request->validated());

        return redirect()->route('medicamentos.index')
            ->with('success', 'Medicamento '.$medicamento->nombre_med.' agregado correctamente.');
    }

    public function edit(Medicamento $medicamento): View
    {
        return view('MEDICAMENTOS.EditarMedicamento', compact('medicamento'));
    }

    public function update(UpdateMedicamentoRequest $request, Medicamento $medicamento): RedirectResponse
    {
        $medicamento->update($request->validated());

        return redirect()->route('medicamentos.index')
            ->with('success', 'Medicamento actualizado correctamente.');
    }
}
