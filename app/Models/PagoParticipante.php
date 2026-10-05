<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoParticipante extends Model
{
    protected $fillable = [
        "pago_id",
        "gasto_id",
        "pago_detalle_id",
        "participante_id",
        "monto",
        "porcentaje",
    ];
}
