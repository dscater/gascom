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

        // Indexamos los gastos para no hacer first() repetidamente
        $gastosIndexados = $pago->pago_gastos->keyBy(function ($gasto) {
            return $gasto->pago_detalle_id . '_' . $gasto->pago_participante_id;
        });

        // Construimos la matriz
        $matriz = $pago->pago_detalles->map(function ($detalle) use (
            $pago,
            $gastosIndexados
        ) {

            return [
                'detalle' => $detalle,

                'participantes' => $pago->pago_participantes->map(
                    function ($participante) use (
                        $detalle,
                        $gastosIndexados
                    ) {

                        $key = $detalle->id . '_' . $participante->id;

                        return [
                            'participante' => $participante,
                            'gasto' => $gastosIndexados->get($key),
                        ];
                    }
                ),
            ];
        });

        // Totales por participante
        $totalesParticipantes = [];

        foreach ($pago->pago_participantes as $participante) {

            $totalesParticipantes[$participante->id] = $pago->pago_gastos
                ->where('pago_participante_id', $participante->id)
                ->sum('monto_pagado');
        }

        $totalGeneral = array_sum($totalesParticipantes);

        // Enviar correo individual
        foreach ($pago->pago_participantes as $pagoParticipante) {

            $totalParticipante =
                $totalesParticipantes[$pagoParticipante->id] ?? 0;

            $datos = [
                "meses" => $meses,
                "mes" => $pago->mes,
                "anio" => $pago->anio,

                "pago" => $pago,
                "matriz" => $matriz,

                "totales_participantes" => $totalesParticipantes,
                "total_general" => $totalGeneral,

                // Información específica del participante
                "pago_participante" => $pagoParticipante,
                "total_participante" => $totalParticipante,
            ];

            Mail::to($pagoParticipante->participante->correo)
                ->send(new DistribucionPagoMail($datos));
        }
    }
}
