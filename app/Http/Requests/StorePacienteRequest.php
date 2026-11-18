<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => 'required|integer|unique:Paciente',
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'fecha_nac' => 'required|date',
            'sexo' => 'required',
            'edad' => 'required|integer|min:0',
            'estatura' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya existe en el sistema.',
            'nombre.required' => 'El nombre es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nac.date' => 'La fecha debe ser válida.',
            'sexo.required' => 'El sexo es obligatorio.',
            'edad.required' => 'La edad es obligatoria.',
            'edad.integer' => 'La edad debe ser un número entero.',
            'edad.min' => 'La edad no puede ser negativa.',
            'estatura.required' => 'La estatura es obligatoria.',
            'estatura.numeric' => 'La estatura debe ser un número.',
            'estatura.min' => 'La estatura no puede ser negativa.',
        ];
    }
}
