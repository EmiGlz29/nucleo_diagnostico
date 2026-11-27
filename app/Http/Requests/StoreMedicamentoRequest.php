<?php

namespace App\Http\Requests;

use App\Models\Administrador;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() instanceof Administrador;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => is_string($this->input('codigo')) ? trim($this->input('codigo')) : $this->input('codigo'),
            'nombre_med' => is_string($this->input('nombre_med')) ? trim($this->input('nombre_med')) : $this->input('nombre_med'),
            'via_admin' => is_string($this->input('via_admin')) ? trim($this->input('via_admin')) : $this->input('via_admin'),
            'presentacion' => is_string($this->input('presentacion')) ? trim($this->input('presentacion')) : $this->input('presentacion'),
            'fecha_cad' => is_string($this->input('fecha_cad')) ? trim($this->input('fecha_cad')) : $this->input('fecha_cad'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => ['bail', 'required', 'integer', 'min:1', 'max:2147483647', Rule::unique('Medicamento', 'codigo')],
            'nombre_med' => ['bail', 'required', 'string', 'max:255'],
            'via_admin' => ['bail', 'required', 'string', 'max:255'],
            'presentacion' => ['bail', 'required', 'string', 'max:255'],
            'fecha_cad' => ['bail', 'required', 'date_format:Y-m-d'],
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
            'fecha_cad.date_format' => 'La fecha de caducidad debe tener un formato válido.',
        ];
    }
}
