{{-- resources/views/checkout/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Checkout</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 py-6">
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($cart->items->isEmpty())
            <div class="p-3 bg-yellow-50 border rounded">Tu carrito está vacío.</div>
            <div class="mt-4">
                <a href="{{ route('cart.show') }}" class="px-3 py-2 bg-gray-200 rounded">Volver al carrito</a>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($cart->items as $item)
                    <div class="flex items-center justify-between border rounded p-3">
                        <div class="text-sm">
                            <div class="font-semibold">{{ $item->tour?->title }}</div>
                            <div class="text-gray-600">
                                {{ optional(optional($item->tourDate)->start_date)->format('d/m/Y') }}
                                —
                                {{ optional(optional($item->tourDate)->end_date)->format('d/m/Y') }}
                                · Cant: {{ $item->qty }}
                            </div>
                        </div>
                        <div class="font-semibold">
                            ${{ number_format($item->subtotal ?? ($item->qty * $item->unit_price), 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 👇 Usamos el accessor del modelo --}}
            @php $total = $cart->total; @endphp

            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">Total a pagar:</div>
                <div class="font-bold text-lg">
                    ${{ number_format($total, 0, ',', '.') }}
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('cart.show') }}" class="px-3 py-2 bg-gray-200 rounded">
                    ← Volver al carrito
                </a>

                <form action="{{ route('checkout.place') }}" method="POST" class="inline">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded">
                        Confirmar y crear orden
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
