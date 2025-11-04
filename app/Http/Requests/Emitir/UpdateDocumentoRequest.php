<?php

namespace App\Http\Requests\Emitir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Documento; // Required for authorization

class UpdateDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the document model from the route
        $documento = $this->route('documento');

        // Check if the authenticated user is the owner of the document.
        // This is a crucial security check.
        // You can expand this logic using a Policy later (e.g., $this->user()->can('update', $documento))
        // [cite: 1478-1481]
        return Auth::check() && Auth::id() === $documento->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 'sometimes' ensures we only validate the field if it's present in the request.
            // This is perfect for "update" forms.
            'tipo_documento_id' => ['sometimes', 'required', 'integer', 'exists:tipos_documento,id'],
            'area_destino_id' => ['sometimes', 'required', 'integer', 'exists:areas,id'],
            'asunto' => ['sometimes', 'required', 'string', 'min:10'],
            'folios' => ['sometimes', 'required', 'integer', 'min:1'],
            'prioridad' => ['sometimes', 'required', 'string', Rule::in(['Normal', 'Urgente'])],

            // 'nullable' allows the file to be absent (not changed) or explicitly set to null.
            'archivo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB Limit

            'parent_id' => ['nullable', 'integer', 'exists:documentos,id'],
        ];
    }
}
