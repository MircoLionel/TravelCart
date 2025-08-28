<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Checkout</h2></x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    <div class="space-y-2 mb-4">
      @foreach($cart->items as $it)
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

    <div class="text-right mb-4">
      <div class="text-lg">Total: <strong>${{ number_format($cart->total, 0, ',', '.') }}</strong></div>
    </div>

    <form method="POST" action="{{ route('checkout.place') }}">
      @csrf
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Confirmar y reservar</button>
    </form>
  </div>
</x-app-layout>