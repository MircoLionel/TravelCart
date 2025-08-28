<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Reserva confirmada</h2>
  </x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    @if(session('ok')) <div class="mb-3 text-green-700">{{ session('ok') }}</div> @endif

    <div class="border rounded p-4">
      <p class="mb-2">
        <strong>Código de orden:</strong> {{ $order->code }}<br>
        <strong>Total:</strong> ${{ number_format($order->total, 0, ',', '.') }}<br>
        <strong>Estado pago:</strong> {{ strtoupper($order->status) }}
      </p>

      @if($order->reservation)
        <p class="mb-4">
          <strong>Locator:</strong> {{ $order->reservation->locator }}<br>
          <strong>Pasajeros:</strong> {{ $order->reservation->qty }}<br>
        </p>
      @endif

      <h3 class="font-semibold mb-2">Detalle</h3>
      <div class="space-y-2">
        @foreach($order->items as $it)
          <div class="border rounded p-3 flex items-center justify-between">
            <div>
              <div class="font-semibold">{{ $it->tour->title }}</div>
              <div class="text-sm text-gray-600">
                {{ $it->tourDate->start_date->format('d/m/Y') }} → {{ $it->tourDate->end_date->format('d/m/Y') }}
                · Cant.: {{ $it->qty }}
              </div>
            </div>
            <div class="font-semibold">${{ number_format($it->subtotal, 0, ',', '.') }}</div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        <a href="{{ route('tours.index') }}" class="underline">Volver al catálogo</a>
      </div>
    </div>
  </div>
</x-app-layout>
