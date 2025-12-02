<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Crear viaje</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('vendor.tours.store') }}" class="space-y-4">
            @csrf
            @include('vendor.tours.partials.form')
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" type="submit">Guardar</button>
        </form>
    </div>
</x-app-layout>
