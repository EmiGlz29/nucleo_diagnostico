<?php

namespace App\Http\Requests;

use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Doctor;
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'codigocita',
            'codigopaciente',
            'codigo_medicamento',
            'diagnostico',
            'dias_tratamiento',
            'horario_suministro',
        ] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'codigocita' => ['required', 'integer', 'min:1'],
            'codigopaciente' => ['required', 'integer', 'min:1'],
            'codigo_medicamento' => ['required', 'integer', Rule::exists('Medicamento', 'codigo')],
            'diagnostico' => ['required', 'string', 'max:20000'],
            'dias_tratamiento' => ['required', 'integer', 'min:1', 'max:365'],
            'horario_suministro' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_medicamento.exists' => 'Selecciona un medicamento registrado.',
            'diagnostico.required' => 'El diagnóstico es obligatorio.',
            'dias_tratamiento.min' => 'El tratamiento debe durar al menos un día.',
            'horario_suministro.required' => 'Indica el horario de suministro.',
        ];
    }
}
