<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .subtitle { color: #64748b; margin-bottom: 20px; }

        .resumen { display: flex; margin-bottom: 24px; }
        .resumen-item { width: 33%; }
        .resumen-item .label { color: #64748b; font-size: 11px; }
        .resumen-item .valor { font-size: 16px; font-weight: bold; }
        .verde { color: #047857; }
        .rojo { color: #b91c1c; }
        .teal { color: #0f766e; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; text-align: left; padding: 8px; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .monto { text-align: right; }
    </style>
</head>
<body>
    <h1>Resumen de {{ $nombreMes }} {{ $year }}</h1>
    <p class="subtitle">{{ $household->name }}</p>

    <div class="resumen">
        <div class="resumen-item">
            <div class="label">Ganancias</div>
            <div class="valor verde">₲ {{ number_format($totalGanancias, 0, ',', '.') }}</div>
        </div>
        <div class="resumen-item">
            <div class="label">Gastos</div>
            <div class="valor rojo">₲ {{ number_format($totalGastos, 0, ',', '.') }}</div>
        </div>
        <div class="resumen-item">
            <div class="label">Balance</div>
            <div class="valor teal">₲ {{ number_format($totalGanancias - $totalGastos, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th class="monto">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $t->date->format('d/m/Y') }}</td>
                    <td>{{ $t->type === 'gasto' ? 'Gasto' : 'Ganancia' }}</td>
                    <td>{{ $t->category->name }}</td>
                    <td>{{ $t->description ?? '-' }}</td>
                    <td class="monto">₲ {{ number_format($t->amount_gs, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>