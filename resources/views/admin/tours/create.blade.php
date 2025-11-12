<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Nuevo tour</h2></x-slot>
  <div class="p-6 max-w-3xl mx-auto">
    <form method="POST" action="{{ route('admin.tours.store') }}" class="space-y-3">
      @csrf
      <label>Título <input name="title" class="w-full border rounded p-2" required></label>
      <label>Descripción <textarea name="description" class="w-full border rounded p-2"></textarea></label>
      <div class="grid grid-cols-3 gap-3">
        <label>Destino <input name="destination" class="w-full border rounded p-2" required></label>
        <label>Origen <input name="origin" class="w-full border rounded p-2"></label>
        <label>Días <input type="number" name="days" min="1" value="1" class="w-full border rounded p-2"></label>
      </div>
      <label>Precio base <input type="number" step="0.01" name="base_price" value="0" class="w-full border rounded p-2"></label>
      <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Activo</label>
      <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Guardar</button>
        <a href="{{ route('admin.tours.index') }}" class="underline">Volver</a>
      </div>
      <form action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <!-- ...tus otros campos (title, description, etc.) ... -->

    <div>
        <label class="block text-sm font-medium mb-1">Imagen</label>
        <input type="file" name="image" class="border rounded p-2 w-full">
        @error('image')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Crear</button>
</form>
    </form>
  </div>
</x-app-layout>
