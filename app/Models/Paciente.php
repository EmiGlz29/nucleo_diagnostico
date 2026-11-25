<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory;

    protected $table = 'Paciente';

    protected $primaryKey = 'codigo';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'fecha_nac',
        'sexo',
        'edad',
        'estatura',
    ];

    protected $hidden = [
        'direccion',
        'telefono',
        'fecha_nac',
        'sexo',
        'edad',
        'estatura',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute(): string
    {
        return $this->nombre;
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'codigopaciente', 'codigo');
    }
}
