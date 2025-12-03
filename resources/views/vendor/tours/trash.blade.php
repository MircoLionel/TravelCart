<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Papelera de viajes</h1>
            <a href="{{ route('vendor.tours.index') }}" class="rounded-lg bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200">Volver a mis viajes</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse($tours as $tour)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $tour->title }}</h2>
                            <p class="text-sm text-gray-600">{{ $tour->destination }}</p>
                            <p class="text-xs text-gray-500">Eliminado el {{ $tour->deleted_at?->format('Y-m-d H:i') }}</p>
                        </div>
                        <a class="text-indigo-600 hover:underline" href="{{ route('vendor.tours.trash.export', $tour->id) }}">Descargar XLSX</a>
                    </div>

                    <div class="text-sm text-gray-700">
                        <p>Fechas cargadas: {{ $tour->dates->count() }}</p>
                        <p>Reservas (incluidas las canceladas): {{ $tour->reservations()->withTrashed()->count() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No hay viajes en la papelera.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
