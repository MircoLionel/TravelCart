<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold tracking-tight">
                ¡Gracias por tu compra!
            </h1>
            <a href="{{ route('tours.index') }}"
               class="px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50">
                ← Volver al catálogo
            </a>
        </div>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto">
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

        {{-- Encabezado de orden --}}
        <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">Código de orden</div>
                    <div class="text-xl font-semibold">{{ $order->code }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Fecha</div>
                    <div class="text-lg">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="text-sm text-gray-600">
                    Estado:
                    @php
                        $badge = match($order->status) {
                            'paid' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                        {{ $order->status }}
                    </span>
                </span>

                <span class="text-sm text-gray-600">
                    Cliente: <strong>{{ $order->user?->name }}</strong> · {{ $order->user?->email }}
                </span>

                @if($order->reservation)
                    <span class="text-sm text-gray-600">
                        Localizador: <strong>{{ $order->reservation->locator }}</strong>
                        (pasajeros: {{ $order->reservation->qty }})
                    </span>
                @endif
            </div>

            <p class="mt-3 text-sm text-gray-600">
                Te enviamos un correo con tu <strong>voucher PDF</strong> a <strong>{{ $order->user?->email }}</strong>.
                Si no lo ves, revisá tu carpeta de spam/promociones.
            </p>

            <button onclick="window.print()"
                    class="mt-3 inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50">
                Imprimir esta página
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Items --}}
            <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Tour</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Cant.</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Precio</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($order->items as $it)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $it->tour?->title }}</div>
                                    <div class="text-xs text-gray-500">Destino: {{ $it->tour?->destination }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($it->tourDate)
                                        {{ $it->tourDate->start_date->format('d/m/Y') }}
                                        →
                                        {{ $it->tourDate->end_date->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">{{ $it->qty }}</td>
                                <td class="px-4 py-3 text-right">
                                    ${{ number_format($it->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    ${{ number_format($it->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-3 text-right font-semibold">Total</td>
                            <td class="px-4 py-3 text-right font-extrabold">
                                ${{ number_format($order->total, 0, ',', '.') }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Resumen / Próximos pasos --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <h2 class="mb-2 text-lg font-semibold">Resumen</h2>
                <ul class="space-y-1 text-sm text-gray-700">
                    <li>Código: <strong>{{ $order->code }}</strong></li>
                    <li>Estado: <strong>{{ $order->status }}</strong></li>
                    <li>Total: <strong>${{ number_format($order->total, 0, ',', '.') }}</strong></li>
                </ul>

                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    @if($order->status === 'pending')
                        <p>Tu orden está <strong>pendiente</strong>. Podés presentar el voucher cuando el pago esté confirmado.</p>
                    @elseif($order->status === 'paid')
                        <p>¡Todo listo! Tu orden está <strong>pagada</strong>. Presentá el voucher el día del viaje.</p>
                    @elseif($order->status === 'cancelled')
                        <p>La orden fue <strong>cancelada</strong>. Si fue un error, contactanos.</p>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="{{ route('tours.index') }}"
                       class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                        Seguir comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('orders.voucher', $order) }}" class="inline-flex px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
         Descargar voucher (PDF)
    </a>
    <a href="{{ route('orders.voucher', $order) }}" class="btn btn-primary">
    Descargar voucher (PDF)
</a>
<a href="{{ route('orders.voucher', $order) }}" class="inline-block px-3 py-2 border rounded">
    Ver voucher / Imprimir
</a>

</x-app-layout>
