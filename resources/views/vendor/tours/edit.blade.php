<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar viaje</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 space-y-3">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        <form method="POST" action="{{ route('vendor.tours.update', $tour) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            @include('vendor.tours.partials.form')
            <div class="flex items-center gap-3">
                <button class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" type="submit">Guardar cambios</button>
            </div>
        </form>
        <a class="text-sm text-red-600 hover:underline" href="{{ route('vendor.tours.confirm-delete', $tour) }}">Eliminar</a>

        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-4 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Fechas y cupos</h2>
            </div>

            <form method="POST" action="{{ route('vendor.tours.dates.store', $tour) }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                @csrf
                <div>
                    <label class="text-sm text-gray-700">Salida</label>
                    <input type="date" name="start_date" class="w-full rounded-lg border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm text-gray-700">Regreso</label>
                    <input type="date" name="end_date" class="w-full rounded-lg border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm text-gray-700">Precio</label>
                    <input type="number" min="0" step="0.01" name="price" class="w-full rounded-lg border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm text-gray-700">Capacidad</label>
                    <input type="number" min="1" name="capacity" class="w-full rounded-lg border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm text-gray-700">Disponible</label>
                    <input type="number" min="0" name="available" class="w-full rounded-lg border-gray-300" placeholder="Por defecto = capacidad">
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded" checked>
                        Activa
                    </label>
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" type="submit">Agregar fecha</button>
                </div>
            </form>

            <div class="space-y-3">
                @forelse($tour->dates as $date)
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 space-y-2">
                        <form method="POST" action="{{ route('vendor.tours.dates.update', [$tour, $date]) }}" class="grid grid-cols-1 gap-3 md:grid-cols-6">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="text-xs text-gray-600">Salida</label>
                                <input type="date" name="start_date" value="{{ $date->start_date?->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Regreso</label>
                                <input type="date" name="end_date" value="{{ $date->end_date?->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Precio</label>
                                <input type="number" min="0" step="0.01" name="price" value="{{ $date->price }}" class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Capacidad</label>
                                <input type="number" min="1" name="capacity" value="{{ $date->capacity }}" class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Disponible</label>
                                <input type="number" min="0" name="available" value="{{ $date->available }}" class="w-full rounded-lg border-gray-300" required>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="is_active" value="1" class="rounded" {{ $date->is_active ? 'checked' : '' }}>
                                    Activa
                                </label>
                                <button class="rounded-lg bg-indigo-600 px-3 py-2 text-white" type="submit">Actualizar</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('vendor.tours.dates.destroy', [$tour, $date]) }}" onsubmit="return confirm('¿Eliminar fecha?');" class="text-right">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:underline" type="submit">Eliminar</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Todavía no cargaste fechas para este viaje.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
