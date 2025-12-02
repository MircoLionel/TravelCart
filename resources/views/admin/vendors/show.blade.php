<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ $vendor->name }} ({{ $vendor->legajo }})</h1>
                <p class="text-sm text-gray-600">{{ $vendor->email }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.vendors.passengers', $vendor) }}" class="rounded-lg border border-indigo-600 px-4 py-2 text-indigo-700 hover:bg-indigo-50">Descargar pasajeros (.xls)</a>
                <a href="{{ route('admin.vendors.index') }}" class="rounded-lg bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6 space-y-4">
        @forelse($tours as $tour)
            @php
                $countPercent = $tour->sales_count ? min(100, round(($tour->sales_count / $maxCount) * 100)) : 0;
                $amountPercent = $tour->sales_amount ? min(100, round(($tour->sales_amount / $maxAmount) * 100)) : 0;
                $vendorGain = (int) round(($tour->sales_amount ?? 0) * 0.13);
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $tour->title }}</h2>
                        <p class="text-sm text-gray-600">{{ $tour->destination }}</p>
                        <p class="text-xs text-indigo-700">Ganancia 13%: ${{ number_format($vendorGain,0,',','.') }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
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
                            <span>Ingresos</span>
                            <span data-raw-amount="{{ $tour->sales_amount ?? 0 }}">${{ number_format($tour->sales_amount ?? 0,0,',','.') }}</span>
                        </div>
                        <div class="mt-1 h-2 w-full rounded-full bg-emerald-100">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $amountPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600">Este proveedor aún no tiene reservas.</p>
        @endforelse
    </div>
</x-app-layout>
