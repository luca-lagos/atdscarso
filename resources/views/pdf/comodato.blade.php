<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comodato #{{ $prestamo->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }

        h1 {
            text-align: center;
            font-size: 18px;
        }

        .info {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td,
        th {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Contrato de Comodato - Préstamo de Equipo Informático</h1>

    <p>Entre <strong>Escuela XXX</strong> y el profesor <strong>{{ $prestamo->user->nombre_completo }}</strong>,
        se acuerda el préstamo del siguiente equipo:</p>

    <div class="info">
        <table>
            <tr>
                <th>Equipo</th>
                <td>{{ $prestamo->inventario->nombre_equipo }}</td>
            </tr>
            <tr>
                <th>Categoría</th>
                <td>{{ $prestamo->inventario->categoria }}</td>
            </tr>
            <tr>
                <th>N° Serie</th>
                <td>{{ $prestamo->inventario->nro_serie }}</td>
            </tr>
            <tr>
                <th>Fecha préstamo</th>
                <td>{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Fecha devolución</th>
                <td>{{ $prestamo->fecha_devolucion?->format('d/m/Y') ?? 'No especificada' }}</td>
            </tr>
        </table>
    </div>

    <p>El profesor asume la responsabilidad del cuidado, buen uso y devolución del equipo en las condiciones expresadas.
    </p>

    <div style="margin-top:60px;">
        <table width="100%">
            <tr>
                <td style="text-align:center;">
                    _________________________<br>
                    Firma Profesor
                </td>
                <td style="text-align:center;">
                    _________________________<br>
                    Firma Responsable Área Informática
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
