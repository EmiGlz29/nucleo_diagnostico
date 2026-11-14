<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class EMPLEADO extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\EMPLEADOFactory> */
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