<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Mis viajes</h1>
            <a href="{{ route('vendor.tours.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">+ Nuevo viaje</a>
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
                        </div>
                        <a href="{{ route('vendor.tours.edit', $tour) }}" class="text-indigo-600 hover:underline">Editar</a>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">Aún no creaste viajes.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
