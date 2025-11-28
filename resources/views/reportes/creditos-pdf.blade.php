<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Créditos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #333; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; }
        .stat-box { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #FF9800; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .header-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Reporte de Créditos Pendientes</h1>
    <div class="header-info">
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>Total Prestado</h3>
            <p>Bs. {{ number_format($totalPrestado, 2) }}</p>
        </div>
        <div class="stat-box">
            <h3>Total Recuperado</h3>
            <p>Bs. {{ number_format($totalRecuperado, 2) }}</p>
        </div>
        <div class="stat-box">
            <h3>Saldo Pendiente</h3>
            <p>Bs. {{ number_format($saldoPendiente, 2) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Plan</th>
                <th>Saldo Inicial</th>
                <th>Saldo Pendiente</th>
            </tr>
        </thead>
        <tbody>
            @foreach($creditos as $credito)
            <tr>
                <td>{{ $credito->nro }}</td>
                <td>{{ $credito->user->name ?? 'N/A' }}</td>
                <td>{{ $credito->fecha->format('d/m/Y') }}</td>
                <td>{{ $credito->plan->nombre ?? 'N/A' }}</td>
                <td>Bs. {{ number_format($credito->saldo_inicial, 2) }}</td>
                <td>Bs. {{ number_format($credito->saldo_final, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
