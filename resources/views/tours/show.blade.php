<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ $tour->title }}
            </h1>

            <a href="{{ route('tours.index') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50">
                ← Volver al catálogo
            </a>
        </div>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto">
        {{-- Flash messages --}}
        @if(session('ok'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-green-800">
                {{ session('ok') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Info del tour --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="mb-2 text-gray-700">{{ $tour->description }}</p>

            <div class="text-sm text-gray-700 mb-4 space-x-2">
                <span>Origen: <strong>{{ $tour->origin ?? '—' }}</strong></span>
                <span>· Destino: <strong>{{ $tour->destination }}</strong></span>
                <span>· Duración: <strong>{{ $tour->days }} días</strong></span>
            </div>

            <p class="mb-4">
                Precio base: <strong>${{ number_format($tour->base_price, 0, ',', '.') }}</strong>
            </p>
        </div>

        {{-- Fechas disponibles --}}
        <h2 class="mt-6 mb-2 text-lg font-semibold">Fechas disponibles</h2>

        <div class="space-y-3">
            @forelse ($tour->dates as $d)
                @php
                    $sinCupo = $d->available <= 0;
                    $inactiva = ! $d->is_active;
                @endphp

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-lg border border-gray-200 bg-white p-4">
                    <div>
                        <div class="font-medium">
                            {{ $d->start_date->format('d/m/Y') }} → {{ $d->end_date->format('d/m/Y') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Cupos: {{ $d->available }}/{{ $d->capacity }}
                            @if($inactiva)
                                · <span class="text-red-600">Fecha inactiva</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Precio</div>
                            <div class="text-lg font-semibold">
                                ${{ number_format($d->price, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Formulario: Agregar al carrito --}}
                        <form method="POST" action="{{ route('cart.add') }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                            {{-- Usamos "date_id". Tu CartController acepta date_id o tour_date_id --}}
                            <input type="hidden" name="date_id" value="{{ $d->id }}">

                            <input type="number" name="qty" value="1" min="1"
                                   class="w-20 rounded border border-gray-300 px-2 py-1">

                            <button type="submit"
                                    @disabled($sinCupo || $inactiva)
                                    class="rounded bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed">
                                Agregar al carrito
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Errores de validación (visibles si falla el POST) --}}
                @error('tour_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('date_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('tour_date_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('qty') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            @empty
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 text-yellow-800">
                    No hay fechas activas para este tour.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
