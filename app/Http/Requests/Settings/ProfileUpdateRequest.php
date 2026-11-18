<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\Administrador;
use App\Models\EMPLEADO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! ($this->user() instanceof EMPLEADO || $this->user() instanceof Administrador) && is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower($this->input('email'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->user() instanceof EMPLEADO || $this->user() instanceof Administrador) {
            return [
                'name' => ['required', 'string', 'max:50'],
            ];
        }

        return $this->profileRules($this->user()->id);
    }
}
