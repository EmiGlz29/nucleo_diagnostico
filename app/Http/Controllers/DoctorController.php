<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor):void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor):void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor):void
    {
        //
    }
}
