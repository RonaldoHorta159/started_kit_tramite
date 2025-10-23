<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // o valida con políticas si aplica
    }

    public function rules(): array
    {
        return [
            'dni' => ['required', 'digits:8', 'unique:users,dni'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'celular' => ['nullable', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'foto_path' => ['nullable', 'string', 'max:255'],
            'primary_area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'rol' => ['required', Rule::in(['Admin', 'Usuario', 'Mesa de Partes'])],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'dni' => $this->dni ? trim($this->dni) : null,
            'email' => $this->email ? trim(strtolower($this->email)) : null,
            'primary_area_id' => ($this->primary_area_id === '' || $this->primary_area_id === null)
                ? null
                : (int) $this->primary_area_id,
        ]);
    }

    public function messages(): array
    {
        return [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'El DNI ya está registrado.',
            'email.unique' => 'El email ya está registrado.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }

    public function attributes(): array
    {
        return [
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'primary_area_id' => 'área principal',
        ];
    }
}
