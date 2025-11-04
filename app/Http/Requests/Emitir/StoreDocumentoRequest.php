<?php

namespace App\Http\Requests\Emitir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDocumentoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede intentar crear un documento.
        // Las reglas más específicas (ej. si el usuario pertenece a un área)
        // se pueden manejar en una Policy o en el controlador.
        return Auth::check();
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Reglas basadas en el formulario de creación del plan [cite: 1642-1649]
        // y el esquema de la BD (tabla 'documentos').
        return [
            'tipo_documento_id' => ['required', 'integer', 'exists:tipos_documento,id'],

            // Esta será el 'area_actual_id' del nuevo documento.
            'area_destino_id'   => ['required', 'integer', 'exists:areas,id'],

            'asunto'            => ['required', 'string', 'min:10'],
            'folios'            => ['required', 'integer', 'min:1'],

            'prioridad'         => ['nullable', 'string', Rule::in(['Normal', 'Urgente'])],

            // 'archivo' es el nombre del input de tipo 'file'
            'archivo'           => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB Límite

            // Opcional, para respuestas [cite: 1574]
            'parent_id'         => ['nullable', 'integer', 'exists:documentos,id'],
        ];
    }

    /**
     * Prepara los datos para la validación.
     */
    protected function prepareForValidation(): void
    {
        // Aquí podrías establecer valores por defecto si es necesario
        // Por ejemplo, si 'prioridad' no viene, establecerla como 'Normal'.
        if (empty($this->prioridad)) {
            $this->merge([
                'prioridad' => 'Normal',
            ]);
        }
    }
}
