<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Voucher {{ $order->code ?? $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1, h2 { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; text-align: left; }
        .right { text-align: right; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h1>Voucher de compra</h1>
    <p class="muted">
        Código: <strong>{{ $order->code ?? $order->id }}</strong><br>
        Fecha: {{ optional($order->created_at)->format('d/m/Y H:i') }}<br>
        Cliente: {{ $order->user->name }} — {{ $order->user->email }}
    </p>

    <h2>Detalle</h2>
    <table>
        <thead>
            <tr>
                <th>Tour</th>
                <th>Salida</th>
                <th>Cant.</th>
                <th class="right">Precio</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @foreach($order->items as $it)
            <tr>
                <td>{{ $it->tour->title ?? '—' }}</td>
                <td>
                    {{ optional($it->tourDate?->start_date)->format('d/m/Y') }}
                    — {{ optional($it->tourDate?->end_date)->format('d/m/Y') }}
                </td>
                <td class="right">{{ $it->qty }}</td>
                <td class="right">${{ number_format($it->unit_price, 0, ',', '.') }}</td>
                <td class="right">${{ number_format($it->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="right">Total</th>
                <th class="right">${{ number_format($order->total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <p class="muted" style="margin-top: 16px;">
        Este voucher confirma tu compra. Presentá este PDF el día del viaje.
    </p>
</body>
</html>
