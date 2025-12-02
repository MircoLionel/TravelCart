<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ $vendor->name }} ({{ $vendor->legajo }})</h1>
                <p class="text-sm text-gray-600">{{ $vendor->email }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.vendors.analytics', $vendor) }}" class="rounded-lg border border-emerald-600 px-4 py-2 text-emerald-700 hover:bg-emerald-50">Descargar métricas (.xlsx)</a>
                <a href="{{ route('admin.vendors.passengers', $vendor) }}" class="rounded-lg border border-indigo-600 px-4 py-2 text-indigo-700 hover:bg-indigo-50">Descargar pasajeros (.xlsx)</a>
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
                $capacityBase = max($tour->total_capacity ?? 0, $tour->sold_seats ?? 0, 1);
                $palette = ['bg-indigo-500', 'bg-emerald-500', 'bg-amber-500', 'bg-sky-500', 'bg-fuchsia-500', 'bg-purple-500'];
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $tour->title }}</h2>
                        <p class="text-sm text-gray-600">{{ $tour->destination }}</p>
                        <p class="text-xs text-gray-500">Capacidad total: {{ $tour->total_capacity ?? 0 }} | Vendidos: {{ $tour->sold_seats ?? 0 }} | Disponibles: {{ $tour->remaining_capacity ?? 0 }}</p>
                        <p class="text-xs text-indigo-700">Ganancia 13%: ${{ number_format($vendorGain,0,',','.') }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs text-gray-600">
                            <span>Ventas</span>
                            <span>{{ $tour->sales_count }} reservas</span>
                        </div>
                        <div class="mt-1 h-3 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="flex h-3 w-full">
                                @foreach($tour->buyer_segments as $idx => $segment)
                                    @php
                                        $width = min(100, round(($segment['seats'] / $capacityBase) * 100));
                                        $color = $palette[$idx % count($palette)];
                                    @endphp
                                    <div class="{{ $color }} h-3" style="width: {{ $width }}%" title="{{ $segment['buyer_name'] }}: {{ $segment['seats'] }} asientos"></div>
                                @endforeach
                                @if(($tour->remaining_capacity ?? 0) > 0)
                                    @php
                                        $width = min(100, round((($tour->remaining_capacity ?? 0) / $capacityBase) * 100));
                                    @endphp
                                    <div class="h-3 bg-gray-300" style="width: {{ $width }}%" title="Disponibles: {{ $tour->remaining_capacity ?? 0 }} asientos"></div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                            @foreach($tour->buyer_segments as $idx => $segment)
                                @php $color = $palette[$idx % count($palette)]; @endphp
                                <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full {{ $color }}"></span>{{ $segment['buyer_name'] }} ({{ $segment['seats'] }} asientos)</span>
                            @endforeach
                            @if(($tour->remaining_capacity ?? 0) > 0)
                                <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-gray-300"></span>Disponibles ({{ $tour->remaining_capacity ?? 0 }})</span>
                            @endif
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
