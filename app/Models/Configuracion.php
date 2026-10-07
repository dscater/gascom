<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre_sistema",
        "alias",
        "razon_social",
        "nit",
        "dir",
        "fono",
        "actividad",
        "correo",
        "logo",
        "qr",
    ];

    protected $casts = [];

    protected $appends = ["url_logo", "logo_b64", "url_qr", "qr_b64"];

    public function getUrlQrAttribute()
    {
        if (!$this->qr) return "";
        return asset("imgs/" . $this->qr);
    }

    public function getQrB64Attribute()
    {
        if (!$this->qr) return "";
        $path = public_path("imgs/" . $this->qr);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }
        return "";
    }

    public function getUrlLogoAttribute()
    {
        return asset("imgs/" . $this->logo);
    }

    public function getLogoB64Attribute()
    {
        $path = public_path("imgs/" . $this->logo);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }
        return "";
    }
}
