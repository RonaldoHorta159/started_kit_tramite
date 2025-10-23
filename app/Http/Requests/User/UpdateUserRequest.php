<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // o valida con políticas si aplica
    }

    public function rules(): array
    {
        // El {user} viene por route-model binding
        $user = $this->route('user'); // instancia de App\Models\User o id

        $userId = is_object($user) ? $user->id : (int) $user;

        return [
            'dni' => ['required', 'digits:8', Rule::unique('users', 'dni')->ignore($userId)],
            'nombres' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'celular' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // opcional
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
            'dni.digits' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'El DNI ya está registrado por otro usuario.',
            'email.unique' => 'El email ya está registrado por otro usuario.',
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
