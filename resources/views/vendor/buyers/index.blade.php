<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Solicitudes de compradores</h1>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Comprador</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Legajo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Estado</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($links as $link)
                        <tr>
                            <td class="px-4 py-2">
                                <div class="font-semibold">{{ $link->buyer?->name }}</div>
                                <div class="text-xs text-gray-600">{{ $link->buyer?->email }}</div>
                            </td>
                            <td class="px-4 py-2 text-sm">{{ $link->legajo ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm">{{ $link->status }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <form method="POST" action="{{ route('vendor.buyers.approve', $link) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="legajo" value="{{ $link->buyer?->legajo }}" class="w-28 rounded border-gray-300 text-sm" placeholder="Legajo" required>
                                    <button class="rounded bg-green-600 px-3 py-1 text-white text-sm">Aprobar</button>
                                </form>
                                <form method="POST" action="{{ route('vendor.buyers.reject', $link) }}" class="inline">
                                    @csrf
                                    <button class="rounded bg-red-600 px-3 py-1 text-white text-sm" type="submit">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-600">No hay solicitudes pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
