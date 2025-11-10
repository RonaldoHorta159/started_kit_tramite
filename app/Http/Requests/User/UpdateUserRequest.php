<?php

namespace App\Http\Requests\User;

use App\Enums\Estado;
use App\Enums\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->rol === Rol::ADMIN;
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
            'rol' => ['required', Rule::enum(Rol::class)],
            'estado' => ['required', Rule::enum(Estado::class)],

            // 🔹 Áreas adicionales (pivot)
            'areas_ids' => ['sometimes', 'array'],
            'areas_ids.*' => ['integer', 'distinct', 'exists:areas,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normaliza primary_area_id
        $primaryAreaId = $this->primary_area_id;
        $primaryAreaId = ($primaryAreaId === '' || $primaryAreaId === null) ? null : (int) $primaryAreaId;

        // Normaliza areas_ids: admite string, número, array; castea a ints y deduplica
        $areas = $this->input('areas_ids', null);
        if ($areas !== null) {
            if (!is_array($areas)) {
                $areas = [$areas];
            }
            $areas = array_values(array_unique(
                array_map(
                    fn($v) => is_numeric($v) ? (int) $v : $v,
                    array_filter($areas, fn($v) => $v !== '' && $v !== null)
                )
            ));
        }

        $this->merge([
            'dni' => $this->dni ? trim($this->dni) : null,
            'email' => $this->email ? trim(strtolower($this->email)) : null,
            'primary_area_id' => $primaryAreaId,
            'areas_ids' => $areas,
        ]);
    }

    public function messages(): array
    {
        return [
            'dni.digits' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'El DNI ya está registrado por otro usuario.',
            'email.unique' => 'El email ya está registrado por otro usuario.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',

            'areas_ids.array' => 'Las áreas adicionales deben enviarse como una lista.',
            'areas_ids.*.integer' => 'Cada área adicional debe ser un ID válido.',
            'areas_ids.*.distinct' => 'Hay áreas adicionales duplicadas.',
            'areas_ids.*.exists' => 'Alguna de las áreas adicionales no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'primary_area_id' => 'área principal',
            'areas_ids' => 'áreas adicionales',
        ];
    }
}
