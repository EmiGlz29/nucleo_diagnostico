<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultaRequest;
use App\Models\Administrador;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Doctor;
use App\Models\Medicamento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    public function create(string $codigocita, string $codigopaciente): View
    {
        $doctor = request()->user();
        abort_unless($doctor instanceof Doctor, 403);

        $cita = $this->findCita($codigocita, $codigopaciente);
        abort_unless((string) $cita->codigodoctor === (string) $doctor->codigo, 403);

        return view('CONSULTAS.CrearConsulta', [
            'cita' => $cita,
            'medicamentos' => Medicamento::query()->orderBy('nombre_med')->get(),
        ]);
    }

    public function store(
        StoreConsultaRequest $request,
    ): RedirectResponse {
        $validated = $request->validated();
        $doctor = $request->user();
        abort_unless($doctor instanceof Doctor, 403);

        $cita = $this->findCita($validated['codigocita'], $validated['codigopaciente']);
        abort_unless((string) $cita->codigodoctor === (string) $doctor->codigo, 403);

        $consulta = DB::transaction(fn (): Consulta => Consulta::query()->create([
            'codigocita' => $cita->codigocita,
            'codigopaciente' => $cita->codigopaciente,
            'codigodoctor' => $doctor->codigo,
            'diagnostico' => $validated['diagnostico'],
            'codigo_medicamento' => $validated['codigo_medicamento'],
            'dias_tratamiento' => $validated['dias_tratamiento'],
            'horario_suministro' => $validated['horario_suministro'],
        ]));

        return redirect()->route('consultas.show', $consulta);
    }

    public function show(Consulta $consulta): View
    {
        $this->authorizeView($consulta, request()->user());

        $consulta->load(['paciente', 'doctor', 'medicamento']);
        $cita = $this->findCita($consulta->codigocita, $consulta->codigopaciente);

        return view('CONSULTAS.MostrarConsulta', compact('consulta', 'cita'));
    }

    public function download(Consulta $consulta)
    {
        $this->authorizeView($consulta, request()->user());

        $consulta->load(['paciente', 'doctor', 'medicamento']);
        $cita = $this->findCita($consulta->codigocita, $consulta->codigopaciente);
        $logoPath = public_path('NDBackground.png');
        abort_unless(is_file($logoPath), 500, 'No se encontró el logo de Núcleo de Diagnóstico.');
        $logoContents = file_get_contents($logoPath);
        abort_if($logoContents === false, 500, 'No se pudo leer el logo de Núcleo de Diagnóstico.');

        $pdf = Pdf::loadView('CONSULTAS.RecetaPdf', [
            'consulta' => $consulta,
            'cita' => $cita,
            'logo' => 'data:image/png;base64,'.base64_encode($logoContents),
        ])->setPaper('a4');

        return $pdf->download('receta.pdf');
    }

    private function findCita(string|int $codigocita, string|int $codigopaciente): Cita
    {
        return Cita::query()
            ->with(['doctor', 'paciente'])
            ->where('codigocita', $codigocita)
            ->where('codigopaciente', $codigopaciente)
            ->firstOrFail();
    }

    private function authorizeView(Consulta $consulta, mixed $user): void
    {
        abort_unless($user instanceof Doctor || $user instanceof Administrador, 403);

        if ($user instanceof Doctor) {
            abort_unless((string) $consulta->codigodoctor === (string) $user->codigo, 403);
        }
    }
}
