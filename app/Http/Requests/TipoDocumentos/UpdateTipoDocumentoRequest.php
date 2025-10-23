<?php

namespace App\Http\Requests\TipoDocumentos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajusta a políticas si corresponde
    }

    public function rules(): array
    {
        // Compatibilidad con distintos nombres de parámetro en la ruta:
        // {tipo_documento} (recomendado con binding), {tipos_documento} o {id}
        $routeParam = $this->route('tipo_documento')
            ?? $this->route('tipos_documento')
            ?? $this->route('id');

        $id = is_object($routeParam) ? ($routeParam->id ?? null) : (int) $routeParam;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tipos_documento', 'nombre')->ignore($id),
            ],
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
