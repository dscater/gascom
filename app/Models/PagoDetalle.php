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

    protected $appends = ["fecha_t"];

    public function getFechaTAttribute()
    {
        if ($this->fecha) {
            return date("d/m/Y", strtotime($this->fecha));
        }

        return "";
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class, 'gasto_id');
    }

    public function pago_participantes()
    {
        return $this->hasMany(PagoParticipante::class, 'pago_detalle_id');
    }
}
