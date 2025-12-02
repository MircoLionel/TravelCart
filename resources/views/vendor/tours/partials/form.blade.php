<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Título</label>
        <input type="text" name="title" value="{{ old('title', $tour->title ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea name="description" class="mt-1 w-full rounded-lg border-gray-300" rows="3">{{ old('description', $tour->description ?? '') }}</textarea>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Precio base</label>
            <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $tour->base_price ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Días</label>
            <input type="number" name="days" value="{{ old('days', $tour->days ?? 1) }}" class="mt-1 w-full rounded-lg border-gray-300" required>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Origen</label>
            <input type="text" name="origin" value="{{ old('origin', $tour->origin ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Destino</label>
            <input type="text" name="destination" value="{{ old('destination', $tour->destination ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tour->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm text-gray-700">Activo</span>
    </div>
</div>
