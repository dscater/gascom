<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ParticipanteGastoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "participante_id" => "required",
            "gasto_id" => "required",
            "porcentaje" => "numeric|min:0|max:100"
        ];
    }

    public function attributes()
    {
        return ["participante_id" => "participante", "gasto_id" => "gasto"];
    }
}
