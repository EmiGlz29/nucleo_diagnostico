<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'Cita';

    protected $primaryKey = 'codigocita';

    protected $keyType = 'int';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'codigocita',
        'codigopaciente',
        'codigodoctor',
        'fechacita',
        'hora',
        'codigoempleado',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'codigodoctor', 'codigo');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'codigopaciente', 'codigo');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(EMPLEADO::class, 'codigoempleado', 'codigo');
    }
}
