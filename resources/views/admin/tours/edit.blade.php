<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Editar: {{ $tour->title }}</h2>
  </x-slot>

  <div class="p-6 max-w-5xl mx-auto">
    @if(session('ok'))    <div class="mb-3 p-2 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="mb-3 p-2 rounded bg-red-50 text-red-700">{{ session('error') }}</div> @endif

   @php $backStatus = request('status', 'all'); @endphp
    <a href="{{ route('admin.tours.index', ['status' => $backStatus]) }}" class="inline-block mb-4 px-3 py-1 bg-gray-200 rounded">
      Volver
    </a>


    @if($tour->trashed())
      <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
        Este tour está en <strong>papelera</strong>.
        <div class="mt-2 flex gap-2">
          {{-- Restaurar --}}
          <form method="POST" action="{{ route('admin.tours.restore', $tour->id) }}">
            @csrf
            <button class="px-3 py-1 bg-green-600 text-white rounded">Restaurar</button>
          </form>

          {{-- Eliminar definitivamente --}}
          <form method="POST" action="{{ route('admin.tours.forceDelete', $tour->id) }}">
            @csrf @method('DELETE')
            <button class="px-3 py-1 bg-red-700 text-white rounded"
                    onclick="return confirm('Esto elimina definitivamente el tour y dependencias. ¿Continuar?')">
              Eliminar definitivamente
            </button>
          </form>
        </div>
      </div>
    @endif

    {{-- Formulario del tour --}}
    <form method="POST" action="{{ route('admin.tours.update', $tour->id) }}" class="space-y-3 mb-8">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-3">
        <label>Título
          <input name="title" class="w-full border rounded p-2" required value="{{ old('title',$tour->title) }}">
        </label>
        <label>Destino
          <input name="destination" class="w-full border rounded p-2" required value="{{ old('destination',$tour->destination) }}">
        </label>
      </div>
      <label>Descripción
        <textarea name="description" class="w-full border rounded p-2">{{ old('description',$tour->description) }}</textarea>
      </label>
      <div class="grid grid-cols-3 gap-3">
        <label>Días
          <input type="number" name="days" min="1" class="w-full border rounded p-2" value="{{ old('days',$tour->days) }}">
        </label>
        <label>Precio base
          <input type="number" step="0.01" name="base_price" class="w-full border rounded p-2" value="{{ old('base_price',$tour->base_price) }}">
        </label>
        <label class="inline-flex items-center gap-2 mt-8">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active',$tour->is_active) ? 'checked' : '' }}>
          Activo
        </label>
      </div>

      <div class="flex items-center gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded" {{ $tour->trashed() ? 'disabled title=Restaure-para-editar' : '' }}>
          Guardar
        </button>
        @unless($tour->trashed())
          {{-- Enviar a papelera --}}
          <form method="POST" action="{{ route('admin.tours.destroy', $tour->id) }}" class="ml-auto">
            @csrf @method('DELETE')
            <button class="px-4 py-2 bg-red-600 text-white rounded"
                    onclick="return confirm('¿Enviar tour a papelera?')">
              Eliminar
            </button>
          </form>
        @endunless
      </div>
    </form>

    {{-- Fechas del tour --}}
    <div class="border rounded p-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Fechas</h3>
        @unless($tour->trashed())
          <a href="{{ route('admin.tours.dates.create', $tour->id) }}" class="px-2 py-1 bg-gray-200 rounded">Nueva fecha</a>
        @endunless
      </div>

      <div class="space-y-2">
        @forelse ($tour->dates as $d)
          <div class="border rounded p-3 flex items-center justify-between {{ $d->trashed() ? 'opacity-70' : '' }}">
            <div>
              {{ $d->start_date->format('d/m/Y') }} → {{ $d->end_date->format('d/m/Y') }}
              · Cupos: {{ $d->available }}/{{ $d->capacity }}
              · ${{ number_format($d->price,0,',','.') }}
              · <span class="px-2 py-0.5 text-xs rounded {{ $d->trashed() ? 'bg-yellow-100 text-yellow-800' : ($d->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700') }}">
                  {{ $d->trashed() ? 'Eliminada' : ($d->is_active ? 'Activa' : 'Inactiva') }}
                </span>
            </div>
            <div class="flex items-center gap-2">
              @if(!$d->trashed() && !$tour->trashed())
                <a href="{{ route('admin.dates.edit', $d->id) }}" class="px-2 py-1 bg-gray-200 rounded">Editar</a>
                <form method="POST" action="{{ route('admin.dates.destroy', $d->id) }}">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 bg-red-600 text-white rounded"
                          onclick="return confirm('¿Enviar fecha a papelera?')">Eliminar</button>
                </form>
              @else
                {{-- Restaurar fecha --}}
                <form method="POST" action="{{ route('admin.dates.restore', $d->id) }}">
                  @csrf
                  <button class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button>
                </form>
                {{-- Eliminar definitivamente fecha --}}
                <form method="POST" action="{{ route('admin.dates.forceDelete', $d->id) }}">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 bg-red-700 text-white rounded"
                          onclick="return confirm('Esto elimina definitivamente la fecha y dependencias. ¿Continuar?')">
                    Eliminar definitivamente
                  </button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <p>Sin fechas.</p>
        @endforelse
      </div>
    </div>
  </div>
</x-app-layout>
