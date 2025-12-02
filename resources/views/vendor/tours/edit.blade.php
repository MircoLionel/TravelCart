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
        <form method="POST" action="{{ route('vendor.tours.destroy', $tour) }}" onsubmit="return confirm('¿Eliminar viaje?')">
            @csrf
            @method('DELETE')
            <button class="text-sm text-red-600 hover:underline" type="submit">Eliminar</button>
        </form>
    </div>
</x-app-layout>
