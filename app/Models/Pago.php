<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        "mes",
        "anio",
        "total",
        "estado"
    ];

    public function pago_detalles()
    {
        return $this->hasMany(PagoDetalle::class, 'pago_id');
    }

    public function pago_participantes()
    {
        return $this->hasMany(PagoParticipante::class, 'pago_id');
    }

    public function pago_gastos()
    {
        return $this->hasMany(PagoGasto::class, 'pago_id');
    }
}
