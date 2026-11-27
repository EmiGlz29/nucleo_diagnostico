<?php

namespace App\Http\Requests;

use App\Models\Administrador;
use App\Models\Medicamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicamentoRequest extends FormRequest
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
        foreach (['codigo', 'nombre_med', 'via_admin', 'presentacion', 'fecha_cad'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $medicamento = $this->route('medicamento');
        abort_unless($medicamento instanceof Medicamento, 404);

        return [
            'codigo' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'max:2147483647',
                Rule::unique('Medicamento', 'codigo')->ignore($medicamento->codigo, 'codigo'),
            ],
            'nombre_med' => ['bail', 'required', 'string', 'max:255'],
            'via_admin' => ['bail', 'required', 'string', 'max:255'],
            'presentacion' => ['bail', 'required', 'string', 'max:255'],
            'fecha_cad' => ['bail', 'required', 'date_format:Y-m-d'],
        ];
    }
}
