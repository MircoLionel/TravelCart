<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Voucher #{{ $order->code ?? $order->id }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        *{ box-sizing: border-box; }
        body{ font-family: Arial, Helvetica, sans-serif; color:#111; margin:0; padding: 24px; }
        .wrap{ max-width: 920px; margin: 0 auto; }
        .header{ display:flex; justify-content: space-between; align-items:center; margin-bottom: 18px; }
        .brand{ font-size: 20px; font-weight: 700; }
        .muted{ color:#6b7280; }
        table{ width: 100%; border-collapse: collapse; }
        th, td{ border-bottom: 1px solid #e5e7eb; text-align: left; padding: 10px 8px; }
        th{ background: #f9fafb; font-weight: 600; }
        .total{ text-align: right; font-weight: 700; font-size: 16px; }
        .note{ margin-top: 22px; font-size: 13px; color:#374151; }
        .btn{ display:inline-block; margin: 14px 0 24px; padding: 10px 14px; border:1px solid #111; text-decoration:none; color:#111; border-radius: 6px; }
        @media print {
            .btn { display:none; }
            body{ padding:0; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="brand">TRAVEL CART</div>
        <div class="muted">
            Voucher: <strong>{{ $order->code ?? $order->id }}</strong><br>
            Fecha: {{ optional($order->created_at)->format('d/m/Y H:i') }}
        </div>
    </div>

    <a href="#" class="btn" onclick="window.print();return false;">Imprimir / Guardar PDF</a>

    <h2>Datos del cliente</h2>
    <table>
        <tr>
            <th>Nombre</th><td>{{ $order->user->name }}</td>
        </tr>
        <tr>
            <th>Email</th><td>{{ $order->user->email }}</td>
        </tr>
        <tr>
            <th>Legajo</th><td>{{ $order->user->legajo ?? '—' }}</td>
        </tr>
        <tr>
            <th>Estado</th><td>{{ $order->status ?? 'paid' }}</td>
        </tr>
    </table>

    <h2 style="margin-top:22px;">Detalle</h2>
    <table>
        <thead>
            <tr>
                <th>Tour</th>
                <th>Fecha</th>
                <th style="text-align:right;">Cant.</th>
                <th style="text-align:right;">Precio</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $it)
                <tr>
                    <td>{{ $it->tour->title ?? '—' }}</td>
                    <td>
                        {{ optional($it->tourDate?->start_date)->format('d/m/Y') }}
                        @if($it->tourDate?->end_date) – {{ optional($it->tourDate?->end_date)->format('d/m/Y') }} @endif
                    </td>
                    <td style="text-align:right;">{{ $it->qty }}</td>
                    <td style="text-align:right;">${{ number_format($it->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align:right;">${{ number_format($it->subtotal,   0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="total">
                    Total: ${{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <p class="note">
        ¡Buen viaje! — Equipo Travel Cart.
        Si no lo ves, revisá tu carpeta de Spam/Promociones.
    </p>
</div>
</body>
</html>
