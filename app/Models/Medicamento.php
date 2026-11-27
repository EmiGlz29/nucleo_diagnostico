<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $codigo
 * @property string $nombre_med
 * @property string $via_admin
 * @property string $presentacion
 * @property string $fecha_cad
 */
class Medicamento extends Model
{
    protected $table = 'Medicamento';

    protected $primaryKey = 'codigo';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre_med',
        'via_admin',
        'presentacion',
        'fecha_cad',
    ];

    protected $hidden = [
        'codigo',
        'nombre_med',
        'via_admin',
        'presentacion',
        'fecha_cad',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute(): string
    {
        return $this->nombre_med;
    }
}
