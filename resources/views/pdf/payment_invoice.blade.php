<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .right { text-align: right; }
        .muted { color: #6b7280; font-size: 11px; }
    </style>
</head>
<body>
<div class="header">
    <div>
        <div class="muted">Factura de pago manual</div>
        <h2 style="margin:4px 0;">Reserva {{ $reservation->locator }}</h2>
        <div class="muted">Generada el {{ now()->format('d/m/Y H:i') }}</div>
    </div>
    <div class="right">
        <div><strong>Proveedor:</strong> {{ $reservation->vendor?->name }}</div>
        <div class="muted">{{ $reservation->vendor?->email }}</div>
    </div>
</div>

<div class="box">
    <div><strong>Tour:</strong> {{ $reservation->tour?->title }} ({{ $reservation->tourDate?->start_date?->format('d/m/Y') }})</div>
    <div class="muted">Cupos: {{ $reservation->qty }}</div>
</div>

<div class="box">
    <div><strong>Titular de la reserva:</strong> {{ $reservation->order?->user?->name ?? 'Comprador' }}</div>
    @if($reservation->order?->user?->email)
        <div class="muted">{{ $reservation->order?->user?->email }}</div>
    @endif
</div>

<div class="box">
    <table class="table">
        <tr>
            <th>Concepto</th>
            <th class="right">Monto</th>
        </tr>
        <tr>
            <td>Total de la reserva</td>
            <td class="right">${{ number_format($reservation->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Comisión del vendedor (13%)</td>
            <td class="right">-${{ number_format($reservation->vendorCommissionAmount(), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Neto a proveedor</strong></td>
            <td class="right"><strong>${{ number_format($reservation->providerNetAmount(), 0, ',', '.') }}</strong></td>
        </tr>
    </table>
</div>

<div class="box">
    <table class="table">
        <tr>
            <th>Pago aplicado</th>
            <th class="right">${{ number_format($payment->amount, 0, ',', '.') }}</th>
        </tr>
        <tr>
            <td class="muted">A nombre de</td>
            <td class="right">{{ $reservation->order?->user?->name ?? 'Titular no disponible' }}</td>
        </tr>
        <tr>
            <td class="muted">Fecha</td>
            <td class="right">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
        </tr>
        @if($payment->note)
            <tr>
                <td class="muted">Nota</td>
                <td class="right">{{ $payment->note }}</td>
            </tr>
        @endif
        <tr>
            <td>Saldo pendiente</td>
            <td class="right">${{ number_format($reservation->outstandingAmount(), 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

<p class="muted">Este comprobante refleja la ganancia del vendedor (13%) y el neto correspondiente al proveedor.</p>
</body>
</html>
