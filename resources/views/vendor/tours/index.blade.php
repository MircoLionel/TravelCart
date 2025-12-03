<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Mis viajes</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('vendor.tours.trash') }}" class="rounded-lg bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200">Papelera</a>
                <a href="{{ route('vendor.tours.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">+ Nuevo viaje</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse($tours as $tour)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $tour->title }}</h2>
                            <p class="text-sm text-gray-600">{{ $tour->destination }}</p>
                            <p class="text-sm text-gray-600">${{ number_format($tour->base_price,0,',','.') }}</p>
                            <p class="text-xs text-indigo-700">Ganancia vendedor (13%): ${{ number_format($tour->base_price * 0.13,0,',','.') }}</p>
                        </div>
                        <a href="{{ route('vendor.tours.edit', $tour) }}" class="text-indigo-600 hover:underline">Editar</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @php
                            $countPercent = $tour->sales_count ? min(100, round(($tour->sales_count / $maxCount) * 100)) : 0;
                            $amountPercent = $tour->sales_amount ? min(100, round(($tour->sales_amount / $maxAmount) * 100)) : 0;
                            $vendorGain = (int) round(($tour->sales_amount ?? 0) * 0.13);
                        @endphp

                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Ventas</span>
                                <span>{{ $tour->sales_count }} reservas</span>
                            </div>
                            <div class="mt-1 h-2 w-full rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $countPercent }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Ingresos (total)</span>
                                <span>${{ number_format($tour->sales_amount ?? 0,0,',','.') }}</span>
                            </div>
                            <div class="mt-1 h-2 w-full rounded-full bg-emerald-100">
                                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $amountPercent }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-indigo-700">Ganancia 13%: ${{ number_format($vendorGain,0,',','.') }}</p>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <a class="text-indigo-600 hover:underline" href="{{ route('vendor.tours.passengers.export', $tour) }}">Descargar pasajeros (.xls)</a>
                            <a class="text-indigo-600 hover:underline" href="{{ route('vendor.reservations.index', ['tour' => $tour->id]) }}">Ver reservas</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">Aún no creaste viajes.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
