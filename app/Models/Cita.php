<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'cita';

    protected $primaryKey = 'codigocita';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'codigocita',
        'codigopaciente',
        'codigodoctor',
        'fechacita',
        'hora',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'codigodoctor', 'codigo');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'codigopaciente', 'codigo');
    }
}
