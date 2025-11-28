<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #4CAF50; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .total { font-weight: bold; font-size: 14px; margin-top: 20px; text-align: right; }
        .header-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Reporte de Ventas</h1>
    <div class="header-info">
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Mesa</th>
                <th>Total (Bs.)</th>
                <th>Tipo Pago</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $venta->fecha_hora->format('d/m/Y H:i') }}</td>
                <td>{{ $venta->user->name ?? 'N/A' }}</td>
                <td>{{ $venta->mesa->codigo ?? 'N/A' }}</td>
                <td>{{ number_format($venta->total, 2) }}</td>
                <td>{{ $venta->tipo_pago }}</td>
                <td>{{ $venta->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Total General: Bs. {{ number_format($totalGeneral, 2) }}</p>
    </div>
</body>
</html>
