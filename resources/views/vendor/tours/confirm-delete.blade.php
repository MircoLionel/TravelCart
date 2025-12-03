<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Confirmar eliminación</h1>
                <p class="text-sm text-gray-600">El viaje tiene reservas activas.</p>
            </div>
            <a href="{{ route('vendor.tours.edit', $tour) }}" class="rounded-lg bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200">Volver</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6 space-y-4">
        @if(session('warn'))
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-amber-800">{{ session('warn') }}</div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-4">
            <h2 class="text-lg font-semibold">{{ $tour->title }}</h2>
            <p class="text-sm text-gray-600">Hay {{ $tour->reservations->count() }} reservas activas. Si confirmas, el viaje irá a la papelera y podrás exportar los datos para devoluciones.</p>

            <div class="overflow-hidden rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700">Localizador</th>
                            <th class="px-4 py-2 text-left text-gray-700">Comprador</th>
                            <th class="px-4 py-2 text-left text-gray-700">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tour->reservations as $reservation)
                            <tr>
                                <td class="px-4 py-2 font-mono text-xs text-gray-700">{{ $reservation->locator }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $reservation->order?->user?->name }}</td>
                                <td class="px-4 py-2 text-gray-700">${{ number_format($reservation->total_amount,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-600">Sin reservas activas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('vendor.tours.destroy', $tour) }}" class="flex items-center gap-3">
                @csrf
                @method('DELETE')
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Enviar a papelera</button>
                <a href="{{ route('vendor.tours.edit', $tour) }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
            </form>
        </div>
    </div>
</x-app-layout>
