<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Voucher - {{ $order->code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#222; }
        h1 { font-size: 20px; margin-bottom: 6px; }
        h2 { font-size: 16px; margin: 18px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        .muted { color: #6b7280; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Voucher de reserva</h1>
    <p class="muted">Código de orden: <strong>{{ $order->code }}</strong></p>
    <p class="muted">Cliente: <strong>{{ $order->user->name }}</strong> — {{ $order->user->email }}</p>
    <p class="muted">Estado: <strong>{{ $order->status }}</strong> — Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <h2>Detalle</h2>
    <table>
        <thead>
            <tr>
                <th>Tour</th>
                <th>Fecha</th>
                <th class="right">Cant.</th>
                <th class="right">Precio</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($order->items as $it)
            <tr>
                <td>{{ $it->tour->title }}</td>
                <td>
                    {{ optional($it->tourDate->start_date)->format('d/m/Y') }}
                    — {{ optional($it->tourDate->end_date)->format('d/m/Y') }}
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

    <p style="margin-top:16px">
        ¡Buen viaje! — Equipo Travel Cart
    </p>
</body>
</html>
