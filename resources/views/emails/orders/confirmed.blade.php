{{-- resources/views/emails/orders/confirmed.blade.php --}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tu orden {{ $order->code ?? $order->id }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f6f7f9;font-family:Arial,Helvetica,sans-serif;color:#111;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f6f7f9;padding:16px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                <tr>
                    <td style="padding:20px 24px;">
                        <h2 style="margin:0 0 8px;font-size:20px;line-height:1.3;">¡Gracias por tu compra!</h2>
                        <p style="margin:0 0 6px;font-size:14px;line-height:1.5;">
                            Código de orden: <strong>{{ $order->code ?? $order->id }}</strong>
                        </p>
                        <p style="margin:0 0 6px;font-size:14px;line-height:1.5;">
                            Cliente: <strong>{{ $order->user->name }}</strong> — {{ $order->user->email }}
                        </p>
                        <p style="margin:0 0 6px;font-size:14px;line-height:1.5;">
                            Estado: <strong>{{ $order->status ?? 'paid' }}</strong>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px 16px;">
                        <h3 style="margin:14px 0 8px;font-size:16px;">Detalle</h3>

                        <table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;border:1px solid #ddd;">
                            <thead>
                                <tr style="background:#f8fafc">
                                    <th align="left" style="font-size:13px;">Tour</th>
                                    <th align="left" style="font-size:13px;">Fecha</th>
                                    <th align="right" style="font-size:13px;">Cant.</th>
                                    <th align="right" style="font-size:13px;">Precio</th>
                                    <th align="right" style="font-size:13px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $it)
                                    <tr style="border-top:1px solid #eee">
                                        <td style="font-size:13px;">{{ $it->tour->title }}</td>
                                        <td style="font-size:13px;">
                                            {{ optional(optional($it->tourDate)->start_date)->format('d/m/Y') }}
                                            —
                                            {{ optional(optional($it->tourDate)->end_date)->format('d/m/Y') }}
                                        </td>
                                        <td align="right" style="font-size:13px;">{{ $it->qty }}</td>
                                        <td align="right" style="font-size:13px;">${{ number_format($it->unit_price, 0, ',', '.') }}</td>
                                        <td align="right" style="font-size:13px;">${{ number_format($it->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="border-top:2px solid #ddd">
                                    <td colspan="4" align="right" style="font-size:13px;"><strong>Total</strong></td>
                                    <td align="right" style="font-size:13px;"><strong>${{ number_format($order->total ?? $order->items->sum('subtotal'), 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>

                {{-- Botón Ver Voucher / Imprimir --}}
                <tr>
                    <td align="center" style="padding:8px 24px 16px;">
                        @php($voucherUrl = route('orders.voucher', $order))
                        <a href="{{ $voucherUrl }}"
                           style="display:inline-block;background:#111;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px;font-size:14px;">
                            Ver voucher / Imprimir
                        </a>
                        <p style="margin:10px 0 0;font-size:12px;color:#555;">
                            Si el botón no funciona, copiá y pegá este enlace en tu navegador:<br>
                            <span style="word-break:break-all;">{{ $voucherUrl }}</span>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:12px 24px 20px;font-size:12px;color:#666;border-top:1px solid #efefef;">
                        <em>¡Buen viaje! — Equipo Travel Cart</em>
                    </td>
                </tr>
            </table>

            <div style="font-size:11px;color:#9ca3af;margin-top:10px;">
                Recibiste este correo porque realizaste una compra en {{ config('app.name') }}.
            </div>
        </td>
    </tr>
</table>

</body>
</html>
