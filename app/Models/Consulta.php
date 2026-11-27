<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consulta extends Model
{
    protected $table = 'Consulta';

    protected $primaryKey = 'codigoconsulta';

    public $timestamps = false;

    protected $fillable = [
        'codigocita',
        'codigopaciente',
        'codigodoctor',
        'diagnostico',
        'codigo_medicamento',
        'dias_tratamiento',
        'horario_suministro',
        'fecha_consulta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_consulta' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'codigopaciente', 'codigo');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'codigodoctor', 'codigo');
    }

    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class, 'codigo_medicamento', 'codigo');
    }
}
