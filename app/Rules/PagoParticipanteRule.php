<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PagoParticipanteRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Debes ingresar al menos 1 participante');
            return;
        }

        foreach ($value as $index => $detalle) {
            // participante_id
            if ($detalle['participante_id'] === "" || $detalle['participante_id'] === null) {
                $fail("Debes seleccionar un participante en la posición" . ($index + 1) . ".");
            }
        }
    }
}
