<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        "mes",
        "anio",
        "total",
    ];

    public function pago_detalles()
    {
        return $this->hasMany(PagoDetalle::class, 'pago_id');
    }
}
