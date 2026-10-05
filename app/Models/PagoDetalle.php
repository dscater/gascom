<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoDetalle extends Model
{
    protected $fillable = [
        "pago_id",
        "gasto_id",
        "monto",
        "fecha",
    ];
}
