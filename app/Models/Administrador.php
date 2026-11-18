<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Administrador extends Authenticatable
{
    protected $table = 'Administrador';

    protected $primaryKey = 'codigo';

    public $timestamps = false;

    protected $hidden = ['contraseña'];

    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return $this->nombre;
    }

    public function getAuthPasswordName(): string
    {
        return 'contraseña';
    }
}
