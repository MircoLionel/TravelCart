<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Proveedores disponibles</h1>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse($vendors as $vendor)
                @php $link = $links[$vendor->id] ?? null; @endphp
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $vendor->name }}</h2>
                            <p class="text-sm text-gray-600">{{ $vendor->email }}</p>
                            @if($link)
                                <p class="mt-1 text-xs text-gray-500">Estado: <strong>{{ $link->status }}</strong></p>
                            @endif
                        </div>
                        @if($link?->status === 'approved')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-800">Conectado</span>
                        @elseif($link?->status === 'pending')
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs text-yellow-800">Pendiente</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('vendors.request', $vendor) }}" class="mt-3 space-y-2">
                        @csrf
                        <label class="block text-sm text-gray-700">Legajo del comprador</label>
                        <input type="text" name="legajo" value="{{ old('legajo', auth()->user()->legajo) }}"
                               class="w-full rounded-lg border-gray-300" placeholder="Obligatorio para aprobación" required>
                        <button class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" type="submit">
                            Solicitar acceso
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-gray-600">Aún no hay proveedores registrados.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
