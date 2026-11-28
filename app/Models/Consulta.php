<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consulta extends Model
{
    protected $table = 'Consulta';

    protected $primaryKey = 'codigo';

    public $timestamps = false;

    protected $fillable = [
        'codigo_cita',
        'codigo_paciente',
        'codigo_doctor',
        'diagnostico',
        'codigo_med',
        'dias_tratamiento',
        'horario_suministro',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'codigo_paciente', 'codigo');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'codigo_doctor', 'codigo');
    }

    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class, 'codigo_med', 'codigo');
    }
}
