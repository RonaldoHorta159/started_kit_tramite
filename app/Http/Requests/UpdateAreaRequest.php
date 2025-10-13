<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // 👈 1. Importar la clase Rule

class UpdateAreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 2. Permitir la acción si el usuario está autenticado
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 3. El 'area' se obtiene del Route Model Binding
        $areaId = $this->area->id;

        return [
            // 4. La regla 'unique' debe ignorar el registro actual
            'nombre' => ['required', 'string', 'max:100', Rule::unique('areas')->ignore($areaId)],
            'codigo' => ['required', 'string', 'max:10', Rule::unique('areas')->ignore($areaId)],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ];
    }
}
