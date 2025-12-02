<p>Hola {{ $reservation->vendor?->name ?? 'proveedor' }},</p>

<p>Registramos un pago manual para la reserva <strong>{{ $reservation->locator }}</strong> del tour <strong>{{ $reservation->tour?->title }}</strong>.</p>

<ul>
    <li>Monto del pago: ${{ number_format($payment->amount, 0, ',', '.') }}</li>
    <li>Total de la reserva: ${{ number_format($reservation->total_amount, 0, ',', '.') }}</li>
    <li>Comisión vendedor (13%): ${{ number_format($reservation->vendorCommissionAmount(), 0, ',', '.') }}</li>
    <li>Saldo pendiente al proveedor: ${{ number_format($reservation->outstandingAmount(), 0, ',', '.') }}</li>
    <li>A nombre de: {{ $reservation->order?->user?->name ?? 'Titular no disponible' }}</li>
    <li>Fecha de pago: {{ $payment->created_at?->format('d/m/Y H:i') }}</li>
</ul>

<p>Adjuntamos el comprobante en PDF para tus registros.</p>

<p>Gracias.</p>
