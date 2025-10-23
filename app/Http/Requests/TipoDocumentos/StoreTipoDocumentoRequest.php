<?php

namespace App\Http\Requests\TipoDocumentos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajusta a políticas si corresponde
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:tipos_documento,nombre'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => $this->nombre ? trim($this->nombre) : null,
            'estado' => $this->estado ? trim($this->estado) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de documento con ese nombre.',
            'estado.in' => 'El estado debe ser Activo o Inactivo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'estado' => 'estado',
        ];
    }
}
