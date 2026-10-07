<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipanteGasto extends Model
{
    protected $fillable = [
        "participante_id",
        "gasto_id",
        "porcentaje",
    ];

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'participante_id');
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class, 'gasto_id');
    }
}
