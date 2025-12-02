<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Reservas</h1>
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Buscar por localizador o pasajero" class="rounded-lg border-gray-300">
                <button class="rounded-lg bg-indigo-600 px-3 py-2 text-white">Buscar</button>
            </form>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="space-y-3">
            @foreach($reservations as $reservation)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-sm text-gray-500">Localizador</div>
                            <div class="text-xl font-semibold">{{ $reservation->locator }}</div>
                            <div class="text-sm text-gray-600">{{ $reservation->tour?->title }} · {{ $reservation->tourDate?->start_date?->format('d/m/Y') }}</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-48">
                                <div class="text-xs text-gray-500">Pago</div>
                                <div class="h-2 w-full rounded-full bg-gray-100">
                                    <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $reservation->paidPercentage() }}%"></div>
                                </div>
                                <div class="text-xs text-gray-600">{{ $reservation->paidPercentage() }}% abonado</div>
                            </div>
                            <a href="{{ route('vendor.reservations.show', $reservation) }}" class="text-indigo-600 hover:underline">Gestionar</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $reservations->links() }}
    </div>
</x-app-layout>
