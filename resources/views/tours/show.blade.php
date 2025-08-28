<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $tour->title }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4">
            {{-- Mensajes flash --}}
            @if(session('ok'))
                <div class="mb-4 rounded bg-green-100 text-green-800 px-3 py-2">
                    {{ session('ok') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded bg-red-100 text-red-800 px-3 py-2">
                    {{ session('error') }}
                </div>
            @endif

            <div class="border rounded-lg p-4">
                <p class="mb-2 text-gray-700">{{ $tour->description }}</p>
                <p class="text-sm mb-4">
                    Origen: <strong>{{ $tour->origin ?? '—' }}</strong> ·
                    Destino: <strong>{{ $tour->destination }}</strong> ·
                    Duración: <strong>{{ $tour->days }} días</strong>
                </p>
                <p class="mb-4">
                    Precio base: <strong>${{ number_format($tour->base_price, 0, ',', '.') }}</strong>
                </p>

                <h3 class="font-semibold mb-2">Fechas disponibles</h3>
                <div class="space-y-2">
                    @forelse ($tour->dates as $d)
                        <div class="flex items-center justify-between border rounded p-3">
                            <div>
                                <span>{{ $d->start_date->format('d/m/Y') }} → {{ $d->end_date->format('d/m/Y') }}</span>
                                <span class="ml-3 text-sm text-gray-600">
                                    Cupos: {{ $d->available }}/{{ $d->capacity }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="font-semibold">
                                    ${{ number_format($d->price, 0, ',', '.') }}
                                </span>

                                @php
                                    $agotado = $d->available <= 0 || $d->start_date->lt(now()->startOfDay());
                                @endphp

                                @auth
                                    @if($agotado)
                                        <button disabled
                                                class="px-3 py-2 bg-gray-300 rounded cursor-not-allowed"
                                                title="Sin cupo o fecha vencida">
                                            Sin cupo
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('cart.add') }}"
                                              class="flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                                            <input type="hidden" name="tour_date_id" value="{{ $d->id }}">
                                            <input type="number" name="qty" value="1" min="1"
                                                   max="{{ $d->available }}"
                                                   class="w-16 border rounded px-2 py-1">
                                            <button class="px-3 py-2 bg-indigo-600 text-white rounded">
                                                Agregar
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                       class="px-3 py-2 bg-indigo-600 text-white rounded">
                                        Iniciar sesión para comprar
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <p>No hay fechas activas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
