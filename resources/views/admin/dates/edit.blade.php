<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Editar fecha · {{ $tour->title }}</h2>
  </x-slot>

  <div class="p-6 max-w-3xl mx-auto">
    @if(session('ok'))    <div class="mb-3 p-2 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="mb-3 p-2 rounded bg-red-50 text-red-700">{{ session('error') }}</div> @endif

    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="inline-block mb-4 px-3 py-1 bg-gray-200 rounded">Volver</a>

    @if($date->trashed())
      <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
        Esta fecha está en <strong>papelera</strong>.
        <div class="mt-2 flex gap-2">
          {{-- Restaurar --}}
          <form method="POST" action="{{ route('admin.dates.restore', $date->id) }}">
            @csrf
            <button class="px-3 py-1 bg-green-600 text-white rounded">Restaurar</button>
          </form>

          {{-- Eliminar definitivamente --}}
          <form method="POST" action="{{ route('admin.dates.forceDelete', $date->id) }}">
            @csrf @method('DELETE')
            <button class="px-3 py-1 bg-red-700 text-white rounded"
                    onclick="return confirm('Esto elimina definitivamente la fecha y dependencias. ¿Continuar?')">
              Eliminar definitivamente
            </button>
          </form>
        </div>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.dates.update', $date->id) }}" class="space-y-3">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-3">
        <label>Inicio
          <input type="date" name="start_date" class="w-full border rounded p-2" required
                 value="{{ $date->start_date->format('Y-m-d') }}" {{ $date->trashed() ? 'disabled' : '' }}>
        </label>
        <label>Fin
          <input type="date" name="end_date" class="w-full border rounded p-2" required
                 value="{{ $date->end_date->format('Y-m-d') }}" {{ $date->trashed() ? 'disabled' : '' }}>
        </label>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <label>Capacidad
          <input type="number" name="capacity" min="1" class="w-full border rounded p-2"
                 value="{{ $date->capacity }}" {{ $date->trashed() ? 'disabled' : '' }}>
        </label>
        <label>Disponible
          <input type="number" name="available" min="0" class="w-full border rounded p-2"
                 value="{{ $date->available }}" {{ $date->trashed() ? 'disabled' : '' }}>
        </label>
        <label>Precio
          <input type="number" step="0.01" name="price" class="w-full border rounded p-2"
                 value="{{ $date->price }}" {{ $date->trashed() ? 'disabled' : '' }}>
        </label>
      </div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" {{ $date->is_active ? 'checked' : '' }} {{ $date->trashed() ? 'disabled' : '' }}>
        Activa
      </label>

      <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded" {{ $date->trashed() ? 'disabled title=Restaure-para-editar' : '' }}>
          Guardar
        </button>
      </div>
    </form>
  </div>
</x-app-layout>
