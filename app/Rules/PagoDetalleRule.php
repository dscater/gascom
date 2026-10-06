<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PagoDetalleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Debes ingresar al menos 1 gasto');
            return;
        }

        foreach ($value as $index => $detalle) {
            // gasto_id
            if ($detalle['gasto_id'] === "" || $detalle['gasto_id'] === null) {
                $fail("El precio del gasto " . ($index + 1) . " es obligatorio.");
            }

            // monto
            if ($detalle['monto'] === "" || $detalle['monto'] === null) {
                $fail("El monto del gasto " . ($index + 1) . " es obligatorio.");
            }

            if (!is_numeric($detalle['monto']) || $detalle['monto'] < 0) {
                $fail("El monto del gasto " . ($index + 1) . " debe ser mayor o igual a 0.");
            }
        }
    }
}
