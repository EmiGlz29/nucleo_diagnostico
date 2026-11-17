<?php

namespace App\Models;

use Database\Factories\EMPLEADOFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string $codigo
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono
 * @property string $fecha_nac
 * @property string $sexo
 * @property string $salario
 * @property string $turno
 * @property string $contraseña
 */ class EMPLEADO extends Authenticatable
{
    /** @use HasFactory<EMPLEADOFactory> */
    use HasFactory;

    protected $table = 'Empleado';

    protected $primaryKey = 'codigo';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'fecha_nac',
        'sexo',
        'salario',
        'turno',
        'contraseña',
    ];

    protected $hidden = [
        'contraseña',
        'direccion',
        'telefono',
        'fecha_nac',
        'sexo',
        'salario',
        'turno',
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
