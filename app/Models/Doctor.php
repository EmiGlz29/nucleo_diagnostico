<?php

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string $codigo
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono
 * @property string $fecha_nac
 * @property string $sexo
 * @property string $especialidad
 * @property string $contraseña
 */
class Doctor extends Authenticatable
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory;

    protected $table = 'Doctor';

    protected $primaryKey = 'codigo';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'fecha_nac',
        'sexo',
        'especialidad',
        'contraseña',
    ];

    protected $hidden = [
        'contraseña',
        'direccion',
        'telefono',
        'fecha_nac',
        'sexo',
        'especialidad',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute(): string
    {
        return $this->nombre;
    }

    public function getAuthPasswordName(): string
    {
        return 'contraseña';
    }
}
