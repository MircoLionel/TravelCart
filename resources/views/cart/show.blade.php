<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Mi Carrito</h2>
  </x-slot>

  <div class="p-6 max-w-4xl mx-auto">
    @if(session('ok')) <div class="mb-3 text-green-700">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="mb-3 text-red-700">{{ session('error') }}</div> @endif

    @if(!$cart || $cart->items->isEmpty())
      <p>Tu carrito está vacío.</p>
      <a href="{{ route('tours.index') }}" class="underline">Volver al catálogo</a>
    @else
      <div class="space-y-3">
        @foreach($cart->items as $it)
          <div class="border rounded p-3 flex items-center justify-between">
            <div>
              <div class="font-semibold">{{ $it->tour->title }}</div>
              <div class="text-sm text-gray-600">
                {{ $it->tourDate->start_date->format('d/m/Y') }}
                → {{ $it->tourDate->end_date->format('d/m/Y') }}
                · Cant.: {{ $it->qty }}
              </div>
            </div>
            <div class="flex items-center gap-4">
              <div class="font-semibold">${{ number_format($it->subtotal, 0, ',', '.') }}</div>
              <form method="POST" action="{{ route('cart.remove', $it) }}">
                @csrf @method('DELETE')
                <button class="px-3 py-2 bg-red-600 text-white rounded">Quitar</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 text-right">
        <div class="text-lg">Total: <strong>${{ number_format($cart->total, 0, ',', '.') }}</strong></div>
        <a href="{{ route('checkout.show') }}"
           class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white rounded">
           Ir al checkout
        </a>
      </div>

      
      
      
      
      
      
      
    @endif
  </div>
</x-app-layout>
