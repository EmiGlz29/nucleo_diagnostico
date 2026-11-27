<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => 'required|integer|unique:Medicamento,codigo',
            'nombre_med' => 'required|string|max:255',
            'via_admin' => 'required|string|max:255',
            'presentacion' => 'required|string|max:255',
            'fecha_cad' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya existe en el sistema.',
            'nombre_med.required' => 'El nombre del medicamento es obligatorio.',
            'via_admin.required' => 'La vía de administración es obligatoria.',
            'presentacion.required' => 'La presentación es obligatoria.',
            'fecha_cad.required' => 'La fecha de caducidad es obligatoria.',
            'fecha_cad.date' => 'La fecha de caducidad debe ser válida.',
        ];
    }
}
