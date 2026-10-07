<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pagos</title>
    <style>
        html,
        body {
            color: black;
        }

        .total {
            background: black;
            color: white;
        }

        .text-md {
            font-size: 1.3em;
        }

        b {
            font-size: 1.15em;
        }
    </style>
</head>

<body>
    <h1>
        Distribución de gastos
    </h1>
    <p>
        {{ $datos['meses'][$datos['mes']] }}
        {{ $datos['anio'] }}
    </p>

    <p>
        Hola
        <strong>
            {{ $datos['pago_participante']->participante->nombre }}
        </strong>.
    </p>

    <p>
        Se ha realizado la distribución de los gastos correspondientes
        al periodo indicado. El monto que te corresponde pagar es:
    </p>
    <div
        style="
    margin: 20px 0;
    padding: 20px;
    background-color: #f3f4f6;
    border: 1px solid #d1d5db;
    text-align: center;
">

        <div style="
        font-size: 16px;
        color: #555;
        margin-bottom: 8px;
    ">
            Monto que te corresponde pagar
        </div>

        <div style="
        font-size: 32px;
        font-weight: bold;
        color: #111;
    ">
            Bs. {{ number_format($datos['total_participante'], 2) }}
        </div>
    </div>
    <p>
        A continuación puedes revisar el detalle de la distribución:
    </p>
    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd;">
                    Gasto
                </th>

                @foreach ($datos['pago']->pago_participantes as $participante)
                    @php
                        $esParticipanteActual = $participante->id == $datos['pago_participante']->id;
                    @endphp

                    <th
                        style="
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: right;
                    {{ $esParticipanteActual ? 'background-color: #fff3cd; font-weight: bold;' : 'background-color: #f5f5f5;' }}
                ">
                        {{ $participante->participante->nombre }}

                        @if ($esParticipanteActual)
                            <br>
                            <small>(TÚ)</small>
                        @endif
                    </th>
                @endforeach

                <th
                    style="
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f5f5f5;
            text-align: right;
        ">
                    TOTAL
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach ($datos['matriz'] as $fila)
                <tr>
                    <td>
                        <strong>
                            {{ $fila['detalle']->gasto->nombre }}
                        </strong>

                        <br>

                        <small>
                            Total:
                            {{ number_format($fila['detalle']->monto, 2) }}
                        </small>
                    </td>

                    @foreach ($fila['participantes'] as $item)
                        @php
                            $esParticipanteActual = $item['participante']->id == $datos['pago_participante']->id;
                        @endphp

                        <td
                            style="
            padding: 10px;
            border: 1px solid #ddd;
            text-align: right;
            {{ $esParticipanteActual ? 'background-color: #fff8dc; font-weight: bold;' : '' }}
        ">
                            {{ number_format($item['gasto']->monto_pagado ?? 0, 2) }}

                            <br>

                            <small>
                                {{ number_format($item['gasto']->porcentaje_pago ?? 0, 8) }}%
                            </small>
                        </td>
                    @endforeach
                    <td class="text-right" class="total">
                        {{ number_format($fila['participantes']->sum(fn($item) => $item['gasto']->monto_pagado ?? 0), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th
                    style="
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f5f5f5;
        ">
                    TOTAL
                </th>

                @foreach ($datos['pago']->pago_participantes as $participante)
                    @php
                        $esParticipanteActual = $participante->id == $datos['pago_participante']->id;
                    @endphp

                    <th
                        style="
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: right;
                    {{ $esParticipanteActual ? 'background-color: #ffe69c; font-weight: bold;' : 'background-color: #f5f5f5;' }}
                ">
                        Bs.
                        {{ number_format($datos['totales_participantes'][$participante->id] ?? 0, 2) }}
                    </th>
                @endforeach

                <th
                    style="
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f5f5f5;
            text-align: right;
        ">
                    Bs.
                    {{ number_format($datos['total_general'], 2) }}
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
