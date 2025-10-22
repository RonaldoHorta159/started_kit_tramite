<?php

// app/Http/Requests/UpdateAreaRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $areaId = $this->route('area')?->id; // por si cambia el binding

        return [
            'nombreArea' => [
                'required',
                'string',
                'max:100',
                Rule::unique('areas', 'nombre')->ignore($areaId)
            ],
            'codigo' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('areas', 'codigo')->ignore($areaId)
            ],
            'estadoArea' => ['required', Rule::in(['ACTIVO', 'INACTIVO'])],
        ];
    }
}
