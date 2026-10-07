<?php

namespace App\Services;

use App\Jobs\EnviaCodigoerificacionJob;
use App\Jobs\EnviaFinalizaInscripcionJob;
use App\Jobs\EnviaInscripcionJob;
use App\Jobs\EnviaPreinscripcionJob;
use App\Mail\CodigoVerificacionMail;
use App\Mail\DistribucionPagoMail;
use App\Mail\FinalizaInscripcionMail;
use App\Mail\InscripcionMail;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreinscripcionMail;
use App\Models\Pago;
use App\Models\Postulante;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EnviarCorreoService
{

    public function __construct()
    {
        $configuracion = Configuracion::first();
        $servidor_correo = $configuracion->envio_email;
        if ($configuracion && $servidor_correo) {
            Config::set(
                [
                    'mail.mailers.default' => $servidor_correo["driver"] ?? 'smtp',
                    'mail.mailers.smtp.host' => $servidor_correo["host"] ?? 'smtp.hostinger.com',
                    'mail.mailers.smtp.port' => $servidor_correo["puerto"] ?? '587',
                    'mail.mailers.smtp.encryption' => $servidor_correo["encriptado"] ?? 'tls',
                    'mail.mailers.smtp.username' => $servidor_correo["correo"] ?? 'mensaje@emsytsrl.com',
                    'mail.mailers.smtp.password' => $servidor_correo["password"] ?? '8Z@d>&kj^y',
                    'mail.from.address' => $servidor_correo["correo"] ?? 'mensaje@emsytsrl.com',
                    'mail.from.name' => $servidor_correo["nombre"] ?? 'GOIRDROP',
                ]
            );
        }
    }


    /**
     * Enviar correo de codigo verificacion login POSTULANTE
     *
     * @param User $user
     * @return void
     */
    public function mailDistribucionPagos(Pago $pago): void
    {

        $mensaje = "PRUEBA";

        $meses = [
            "01" => "Enero",
            "02" => "Febrero",
            "03" => "Marzo",
            "04" => "Abril",
            "05" => "Mayo",
            "06" => "Junio",
            "07" => "Julio",
            "08" => "Agosto",
            "09" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre",
        ];

        $matriz = $pago->pago_detalles->map(function ($detalle) use ($pago) {

            return [
                'detalle' => $detalle,

                'participantes' => $pago->pago_participantes->map(function ($participante) use ($detalle, $pago) {

                    $gasto = $pago->pago_gastos
                        ->first(function ($item) use ($detalle, $participante) {
                            return $item->pago_detalle_id == $detalle->id
                                && $item->pago_participante_id == $participante->id;
                        });

                    return [
                        'participante' => $participante,
                        'gasto' => $gasto,
                    ];
                }),
            ];
        });

        $totalesParticipantes = [];

        foreach ($pago->pago_participantes as $participante) {
            $totalesParticipantes[$participante->id] = $pago->pago_gastos
                ->where('pago_participante_id', $participante->id)
                ->sum('monto_pagado');
        }

        $totalGeneral = array_sum($totalesParticipantes);

        $datos = [
            "mensaje" => $mensaje,
            "meses" => $meses,
            "mes" => $pago->mes,
            "anio" => $pago->anio,
            "pago" => $pago,
            "matriz" => $matriz,
            "totales_participantes" => $totalesParticipantes,
            "total_general" => $totalGeneral,
        ];

        foreach ($pago->pago_participantes as $participante) {
            // Log::debug($participante->participante->correo);

            Mail::to($participante->participante->correo)
                ->send(new DistribucionPagoMail($datos));
        }

        // EnviaCodigoerificacionJob::dispatch($datos, $user);
    }
}
