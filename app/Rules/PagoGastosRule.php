<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PagoGastosRule implements ValidationRule
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

        foreach ($value as $index => $item) {
            // gasto_id
            if ($item['gasto_id'] === "" || $item['gasto_id'] === null) {
                $fail("El precio del gasto " . ($index + 1) . " es obligatorio.");
            }

            // monto_pagado
            if ($item['monto_pagado'] === "" || $item['monto_pagado'] === null) {
                $fail("El monto_pagado del gasto " . ($index + 1) . " es obligatorio.");
            }

            if (!is_numeric($item['monto_pagado']) || $item['monto_pagado'] < 0) {
                $fail("El monto_pagado del gasto " . ($index + 1) . " debe ser mayor o igual a 0.");
            }

            // porcentaje_pago
            if ($item['porcentaje_pago'] === "" || $item['porcentaje_pago'] === null) {
                $fail("El porcentaje_pago del gasto " . ($index + 1) . " es obligatorio.");
            }

            if (!is_numeric($item['porcentaje_pago']) || $item['porcentaje_pago'] < 0) {
                $fail("El porcentaje_pago del gasto " . ($index + 1) . " debe ser mayor o igual a 0.");
            }
        }
    }
}
