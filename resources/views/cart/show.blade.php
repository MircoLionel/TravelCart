{{-- resources/views/cart/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Mi Carrito</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 py-6">
        @if (session('ok'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('ok') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($cart->items->isEmpty())
            <div class="p-3 bg-yellow-50 border rounded">Tu carrito está vacío.</div>
            <div class="mt-4">
                <a href="{{ route('tours.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Ir al catálogo</a>
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

                        <div class="flex items-center gap-3">
                            <div class="w-24 text-right">
                                ${{ number_format($item->subtotal ?? ($item->qty * $item->unit_price), 0, ',', '.') }}
                            </div>

                            <form action="{{ route('cart.remove', $item) }}" method="POST"
                                  onsubmit="return confirm('¿Quitar este item?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 bg-red-600 text-white rounded">Quitar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Opción A: calcular total en la vista (este es el snippet que preguntaste) --}}
            @php
                $total = $cart->items->sum(fn($i) => (int) $i->qty * (int) $i->unit_price);
            @endphp

            {{-- Opción B: usar accessor $cart->total (si lo implementaste en el modelo) --}}
            {{-- @php $total = $cart->total; @endphp --}}

            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">Total:</div>
                <div class="font-bold text-lg">
                    ${{ number_format($total, 0, ',', '.') }}
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('checkout.show') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">
                    Ir al checkout
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
