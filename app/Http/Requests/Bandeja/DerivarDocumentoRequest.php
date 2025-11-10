<?php

namespace App\Http\Requests\Bandeja;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DerivarDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Se podría añadir una lógica más compleja, como verificar que el usuario
        // pertenece al área actual del documento. Esto se hará en el controlador por ahora.
        return $this->user()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $documento = $this->route('documento');

        return [
            'area_destino_id' => [
                'required',
                'integer',
                'exists:areas,id',
                // Evita que se derive al área actual
                Rule::notIn([$documento->area_actual_id]),
            ],
            'proveido' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'area_destino_id.required' => 'Debe seleccionar un área de destino.',
            'area_destino_id.exists' => 'El área de destino no es válida.',
            'area_destino_id.not_in' => 'No puede derivar el documento a la misma área.',
        ];
    }
}
