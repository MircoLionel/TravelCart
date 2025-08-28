<x-mail::message>
# ¡Gracias por tu compra!

Hola **{{ $order->user->name }}**, tu reserva fue generada correctamente.  
Adjuntamos tu **voucher PDF** con todos los detalles.

<x-mail::panel>
**Código de orden:** {{ $order->code }}  
**Estado:** {{ ucfirst($order->status) }}  
**Total:** ${{ number_format($order->total, 0, ',', '.') }}
</x-mail::panel>

@isset($order->reservation)
<x-mail::panel>
**Localizador:** {{ $order->reservation->locator }}  
**Cantidad de pasajeros:** {{ $order->reservation->qty }}
</x-mail::panel>
@endisset

**Resumen**
@foreach($order->items as $it)
- **{{ $it->tour->title ?? '—' }}**  
  @if($it->tourDate)
  Fecha: {{ $it->tourDate->start_date->format('d/m/Y') }} → {{ $it->tourDate->end_date->format('d/m/Y') }}  
  @endif
  Cantidad: {{ $it->qty }} · Precio: ${{ number_format($it->unit_price, 0, ',', '.') }} · Subtotal: ${{ number_format($it->subtotal, 0, ',', '.') }}
@endforeach

<x-mail::button :url="route('orders.show', $order->id)">
Ver mi reserva
</x-mail::button>

Si necesitás ayuda, respondé a este correo.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>