<?php

namespace App\Http\Requests;

use App\Rules\PagoDetalleRule;
use App\Rules\PagoGastosRule;
use App\Rules\PagoParticipanteRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Override;

class PagoStoreRequest extends FormRequest
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
        Log::debug($this);

        return [
            "mes" => "required",
            "anio" => "required",
            "total" => "required",
            "pago_detalles" => ["required", "array", "min:1", new PagoDetalleRule()],
            "pago_participantes" => ["required", "array", "min:1", new PagoParticipanteRule()],
            "pago_gastos" => ["required", "array", "min:1", new PagoGastosRule()],
        ];
    }

    public function attributes()
    {
        return ["mes" => "Mes", "anio" => "año"];
    }
}
