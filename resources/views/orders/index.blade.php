{{-- resources/views/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis órdenes') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($orders->count() === 0)
                        <p class="text-gray-600">Aún no tenés órdenes.</p>
                        <a href="{{ route('tours.index') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Ir al catálogo
                        </a>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="px-4 py-3 font-mono">{{ $order->code }}</td>
                                            <td class="px-4 py-3">{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded text-xs
                                                    @if($order->status === 'paid') bg-green-100 text-green-700
                                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700
                                                    @else bg-gray-100 text-gray-700 @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                ${{ number_format($order->total ?? ($order->items->sum('subtotal')), 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('orders.show', $order) }}"
                                                   class="inline-flex items-center px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                                    Ver detalle
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
