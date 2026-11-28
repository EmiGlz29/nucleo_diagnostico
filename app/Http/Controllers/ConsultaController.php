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
    public function index(): View
    {
        $user = request()->user();
        abort_unless($user instanceof Doctor || $user instanceof Administrador, 403);

        if ($user instanceof Doctor) {
            $consultas = Consulta::query()
                ->where('codigo_doctor', $user->codigo)
                ->orderBy('codigo', 'desc')
                ->get();
        } else {
            $consultas = Consulta::query()
                ->orderBy('codigo', 'desc')
                ->get();
        }

        return view('CONSULTAS.ListarConsultas', compact('consultas'));
    }

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

        $cita = $this->findCita($validated['codigo_cita'], $validated['codigo_paciente']);
        abort_unless((string) $cita->codigodoctor === (string) $doctor->codigo, 403);

        $consulta = DB::transaction(fn (): Consulta => Consulta::query()->create([
            'codigo_cita' => $validated['codigo_cita'],
            'codigo_paciente' => $validated['codigo_paciente'],
            'codigo_doctor' => $doctor->codigo,
            'diagnostico' => $validated['diagnostico'],
            'codigo_med' => $validated['codigo_med'],
            'dias_tratamiento' => $validated['dias_tratamiento'],
            'horario_suministro' => $validated['horario_suministro'],
        ]));

        return redirect()->route('consultas.show', $consulta);
    }

    public function show(Consulta $consulta): View
    {
        $this->authorizeView($consulta, request()->user());

        $consulta->load(['paciente', 'doctor', 'medicamento']);
        $cita = $this->findCita($consulta->codigo_cita, $consulta->codigo_paciente);

        return view('CONSULTAS.MostrarConsulta', compact('consulta', 'cita'));
    }

    public function download(Consulta $consulta)
    {
        $this->authorizeView($consulta, request()->user());

        $consulta->load(['paciente', 'doctor', 'medicamento']);
        $cita = $this->findCita($consulta->codigo_cita, $consulta->codigo_paciente);
        $logoPath = public_path('NDBackground.png');
        abort_unless(is_file($logoPath), 500, 'No se encontró el logo de Núcleo de Diagnóstico.');
        $logoContents = file_get_contents($logoPath);
        abort_if($logoContents === false, 500, 'No se pudo leer el logo de Núcleo de Diagnóstico.');

        $pdf = Pdf::loadView('CONSULTAS.RecetaPdf', [
            'consulta' => $consulta,
            'cita' => $cita,
            'logo' => 'data:image/png;base64,'.base64_encode($logoContents),
        ])->setPaper('a4');

        $filename = "receta_consulta_{$consulta->codigo}.pdf";
        $filepath = public_path("recetas/{$filename}");
        $pdf->save($filepath);

        return $pdf->download($filename);
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
            abort_unless((string) $consulta->codigo_doctor === (string) $user->codigo, 403);
        }
    }
}
