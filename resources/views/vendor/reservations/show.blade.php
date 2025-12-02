<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Reserva {{ $reservation->locator }}</h1>
            <a href="{{ route('vendor.reservations.index') }}" class="text-indigo-600 hover:underline">← Volver</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">Tour</div>
                    <div class="text-lg font-semibold">{{ $reservation->tour?->title }}</div>
                    <div class="text-sm text-gray-600">{{ $reservation->tourDate?->start_date?->format('d/m/Y') }} → {{ $reservation->tourDate?->end_date?->format('d/m/Y') }}</div>
                </div>
                <div class="w-64">
                    <div class="text-xs text-gray-500">Pago</div>
                    <div class="h-2 w-full rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $reservation->paidPercentage() }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600">{{ $reservation->paidPercentage() }}% abonado</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <h2 class="text-lg font-semibold mb-2">Pasajeros ({{ $reservation->passengers->count() }}/{{ $reservation->qty }})</h2>
                <ul class="space-y-2 text-sm text-gray-700">
                    @foreach($reservation->passengers as $p)
                        <li class="flex items-center justify-between rounded border border-gray-100 p-2">
                            <div>
                                <div class="font-semibold">{{ $p->first_name }} {{ $p->last_name }}</div>
                                <div class="text-xs text-gray-600">DNI {{ $p->document_number }}</div>
                            </div>
                            <div class="text-xs text-gray-500">{{ $p->birth_date?->format('d/m/Y') }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-4">
                <h2 class="text-lg font-semibold">Gestión</h2>
                <form method="POST" action="{{ route('vendor.reservations.update', $reservation) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="text-sm text-gray-700">Estado</label>
                            <select name="status" class="mt-1 w-full rounded-lg border-gray-300">
                                @foreach(['pending','confirmed','cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($reservation->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Cupos</label>
                            <input type="number" name="qty" value="{{ $reservation->qty }}" min="1" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-white" type="submit">Guardar</button>
                </form>

                <form method="POST" action="{{ route('vendor.reservations.destroy', $reservation) }}" onsubmit="return confirm('¿Cancelar y liberar disponibilidad?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline" type="submit">Eliminar reserva</button>
                </form>

                <div class="border-t pt-3">
                    <h3 class="text-sm font-semibold text-gray-800">Registrar pago parcial</h3>
                    <form method="POST" action="{{ route('vendor.reservations.payments', $reservation) }}" class="mt-2 space-y-2">
                        @csrf
                        <input type="number" name="amount" min="1" class="w-full rounded border-gray-300" placeholder="Monto" required>
                        <input type="text" name="note" class="w-full rounded border-gray-300" placeholder="Nota opcional">
                        <button class="rounded bg-green-600 px-3 py-2 text-white" type="submit">Registrar pago</button>
                    </form>
                    <ul class="mt-3 space-y-1 text-sm text-gray-700">
                        @foreach($reservation->payments as $payment)
                            <li class="flex items-center justify-between">
                                <span>${{ number_format($payment->amount,0,',','.') }} · {{ $payment->created_at->format('d/m/Y H:i') }}</span>
                                <span class="text-xs text-gray-500">{{ $payment->note }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
