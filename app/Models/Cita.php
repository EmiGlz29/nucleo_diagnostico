<?php

namespace App\Models;

use Date;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CitaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @property integer $codigocita
 * @property integer $codigopaciente
 * @property integer $codigodoctor
 * @property string $fechacita
 * @property string $hora
 * @property integer $codigoempleado
 */
class Cita extends Model
{
    /** @use HasFactory<CitaFactory> */
    use HasFactory;
    protected $table = 'Cita';
    protected $foreignKey = 'codigopaciente';
    protected $foreignKeyDoctor = 'codigodoctor';
    protected $foreignKeyEmpleado = 'codigoempleado';
    protected $primaryKey = 'codigocita';
    public $timestamps = false;
    protected $fillable = [
        'codigocita',
        'codigopaciente',
        'codigodoctor',
        'fechacita',
        'hora',
        'codigoempleado',
    ];

    protected $hidden = [
        'codigocita',
        'codigopaciente',
        'codigodoctor',
        'fechacita',
        'hora',
        'codigoempleado',
    ];

    protected $appends = [
        'name',
    ];

}
