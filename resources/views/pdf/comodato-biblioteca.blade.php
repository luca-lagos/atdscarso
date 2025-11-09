<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comodato Biblioteca #{{ $prestamo->id }}</title>
    <style>
        @page {
            margin: 24mm 18mm 24mm 18mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #111;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin: 0 0 8px;
        }

        h2 {
            font-size: 14px;
            margin: 18px 0 8px;
        }

        p {
            margin: 6px 0;
            line-height: 1.35;
        }

        .muted {
            color: #555;
        }

        .small {
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .info {
            margin: 12px 0 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .no-border th,
        .no-border td {
            border: 0;
            padding: 0;
        }

        .signs td {
            border: 0;
            padding: 32px 8px 0;
        }

        .sign-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            width: 100%;
        }

        .flex {
            display: flex;
            gap: 8px;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="small muted">Escuela XXX</div>
        <h1>Contrato de Comodato - Préstamo de Libro</h1>
        <div class="small muted">N° {{ $prestamo->id }}</div>
    </div>

    <p>
        Entre <strong>Escuela XXX</strong> y
        <strong>
            {{ $prestamo->user->name ?? 'Usuario' }}
        </strong>,
        se acuerda el préstamo en calidad de comodato del siguiente material bibliográfico:
    </p>

    <div class="info">
        <h2>Datos del libro</h2>
        <table>
            <tr>
                <th style="width: 28%;">Título</th>
                <td>{{ $prestamo->inventario_biblioteca->titulo ?? '' }}</td>
            </tr>
            <tr>
                <th>Autor</th>
                <td>{{ $prestamo->inventario_biblioteca->autor ?? '' }}</td>
            </tr>
            <tr>
                <th>ISBN</th>
                <td>{{ $prestamo->inventario_biblioteca->isbn ?? '—' }}</td>
            </tr>
            <tr>
                <th>Categoría</th>
                <td>{{ $prestamo->inventario_biblioteca->categoria ?? '—' }}</td>
            </tr>
            <tr>
                <th>Editorial</th>
                <td>{{ $prestamo->inventario_biblioteca->editorial ?? '—' }}</td>
            </tr>
            <tr>
                <th>N° edición / Año</th>
                <td>
                    {{ $prestamo->inventario_biblioteca->numero_edicion ?? '—' }}
                    @php
                        $anio = $prestamo->inventario_biblioteca->fecha_edicion ?? null;
                    @endphp
                    @if ($anio)
                        — {{ $anio }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="info">
        <h2>Plazos del préstamo</h2>
        <table>
            <tr>
                <th style="width: 28%;">Fecha de préstamo</th>
                <td>{{ optional($prestamo->fecha_prestamo)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Fecha de vencimiento</th>
                <td>{{ optional($prestamo->fecha_vencimiento)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Fecha de devolución</th>
                <td>{{ optional($prestamo->fecha_devolucion)->format('d/m/Y') ?? 'No registrada' }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td style="text-transform: capitalize;">{{ $prestamo->estado ?? 'pendiente' }}</td>
            </tr>
        </table>
    </div>

    @if (!empty($prestamo->observaciones))
        <div class="info">
            <h2>Observaciones</h2>
            <table>
                <tr>
                    <td>{{ $prestamo->observaciones }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="info small">
        <h2>Condiciones del comodato</h2>
        <ol>
            <li>El material bibliográfico se entrega en buen estado y deberá devolverse en las mismas condiciones.</li>
            <li>El plazo de préstamo y su eventual renovación deben respetarse; el atraso puede generar sanciones.</li>
            <li>En caso de daño o pérdida, el usuario deberá reponer el material o su valor según normativa
                institucional.</li>
            <li>Este comodato es intransferible; el usuario es el único responsable del uso y custodia del material.
            </li>
        </ol>
    </div>

    <div class="info">
        <table class="no-border" width="100%">
            <tr>
                <td class="small">
                    Lugar y fecha de emisión:<br>
                    _______________________________
                </td>
                <td class="small right">
                    Documento generado por el sistema — {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px;">
        <table width="100%" class="signs">
            <tr>
                <td style="text-align:center; width: 50%;">
                    _______________________________<br>
                    Firma del Usuario
                </td>
                <td style="text-align:center; width: 50%;">
                    _______________________________<br>
                    Firma Responsable de Biblioteca
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
