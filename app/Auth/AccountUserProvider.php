<?php

namespace App\Auth;

use App\Models\Administrador;
use App\Models\Doctor;
use Illuminate\Auth\EloquentUserProvider;

class AccountUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $type = session('auth.account_type', 'empleado');

        if ($type === 'administrador') {
            return Administrador::query()->whereKey($identifier)->first();
        }

        if ($type === 'doctor') {
            return Doctor::query()->whereKey($identifier)->first();
        }

        return $type === 'empleado' ? parent::retrieveById($identifier) : null;
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        if (session('auth.account_type', 'empleado') !== 'empleado') {
            return null;
        }

        return parent::retrieveByToken($identifier, $token);
    }
}
