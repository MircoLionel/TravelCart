<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Nueva fecha · {{ $tour->title }}</h2>
  </x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    @if(session('ok'))    <div class="mb-3 p-2 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="mb-3 p-2 rounded bg-red-50 text-red-700">{{ session('error') }}</div> @endif

    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="inline-block mb-4 px-3 py-1 bg-gray-200 rounded">Volver</a>

    <form method="POST" action="{{ route('admin.tours.dates.store', $tour->id) }}" class="space-y-3">
      @csrf
      <div class="grid grid-cols-2 gap-3">
        <label>Inicio <input type="date" name="start_date" class="w-full border rounded p-2" required></label>
        <label>Fin    <input type="date" name="end_date"   class="w-full border rounded p-2" required></label>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <label>Capacidad  <input type="number" name="capacity"  min="1" class="w-full border rounded p-2" required></label>
        <label>Disponible <input type="number" name="available" min="0" class="w-full border rounded p-2" required></label>
        <label>Precio     <input type="number" step="0.01" name="price" class="w-full border rounded p-2" required></label>
      </div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" checked> Activa
      </label>

      <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Crear</button>
      </div>
    </form>
  </div>
</x-app-layout>
