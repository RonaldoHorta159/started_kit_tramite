<?php

namespace App\Http\Requests;

use App\Enums\Estado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->rol === 'Admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 3. Definir las reglas de validación para los campos del área
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:areas,nombre'],
            'codigo' => ['nullable', 'string', 'max:10', 'unique:areas,codigo'],
            'estado' => ['required', Rule::enum(Estado::class)],
        ];
    }
}
