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
    <h1>Pagos {{ $datos['meses'][$datos['mes']] }} {{ $datos['anio'] }}</h1>
    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Gasto</th>

                @foreach ($datos['pago']->pago_participantes as $participante)
                    <th class="text-right">
                        {{ $participante->participante->nombre }}
                    </th>
                @endforeach

                <th class="text-right">
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
                        <td class="text-right">
                            {{ number_format($item['gasto']->monto_pagado ?? 0, 2) }}

                            <br>

                            <small>
                                {{ number_format($item['gasto']->porcentaje_pago ?? 0, 2) }}%
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
            <tr class="total">
                <th>
                    TOTAL
                </th>

                @foreach ($datos['pago']->pago_participantes as $participante)
                    <th class="text-right">
                        {{ number_format($datos['totales_participantes'][$participante->id] ?? 0, 2) }}
                    </th>
                @endforeach

                <th class="text-right">
                    {{ number_format($datos['total_general'], 2) }}
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
