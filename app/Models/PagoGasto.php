<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoGasto extends Model
{
    protected $fillable = [
        "pago_id",
        "pago_detalle_id",
        "participante_id",
        "pago_participante_id",
        "gasto_id",
        "porcentaje_pago",
        "monto_pagado",
        "estado",
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function pago_detalle()
    {
        return $this->belongsTo(PagoDetalle::class, 'pago_detalle_id');
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'participante_id');
    }

    public function pago_participante()
    {
        return $this->belongsTo(PagoParticipante::class, 'pago_participante_id');
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class, 'gasto_id');
    }
}
