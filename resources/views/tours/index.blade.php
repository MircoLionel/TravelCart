<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Catálogo de Tours</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <form method="GET" class="mb-6">
                <input type="text" name="destination" value="{{ request('destination') }}"
                       placeholder="Buscar por destino..."
                       class="border rounded px-3 py-2 w-full md:w-1/2">
            </form>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($tours as $tour)
                    <a href="{{ route('tours.show', $tour) }}" class="block border rounded-lg p-4 hover:shadow">
                        <h3 class="text-lg font-semibold mb-1">{{ $tour->title }}</h3>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $tour->destination }} · {{ $tour->days }} días
                        </p>
                        @php
                            $next = $tour->dates->first();
                        @endphp
                        @if($next)
                            <p class="text-sm">
                                Próxima salida: <strong>{{ $next->start_date->format('d/m/Y') }}</strong>
                                · Cupos disp.: <strong>{{ $next->available }}</strong>
                            </p>
                        @else
                            <p class="text-sm text-red-600">Sin fechas próximas</p>
                        @endif
                    </a>
                @empty
                    <p>No hay tours activos.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $tours->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
