<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoParticipante extends Model
{
    protected $fillable = [
        "pago_id",
        "participante_id",
        "correo_enviado",
        "total",
        "estado" // PENDIENTE, PAGADO
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'participante_id');
    }
}
