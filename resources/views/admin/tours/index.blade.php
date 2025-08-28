<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Admin · Tours</h2>
  </x-slot>

  <div class="p-6 max-w-6xl mx-auto">
    @if(session('ok'))    <div class="mb-3 p-2 rounded bg-green-50 text-green-700">{{ session('ok') }}</div> @endif
    @if(session('error')) <div class="mb-3 p-2 rounded bg-red-50 text-red-700">{{ session('error') }}</div> @endif

    {{-- Filtro: Todos / Activos / Papelera --}}
    <div class="flex items-center justify-between mb-4">
      <div class="flex gap-2">
        @php
          $tabClasses = 'px-3 py-1 rounded border';
          $activeTab  = 'bg-indigo-600 text-white border-indigo-600';
          $idleTab    = 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
        @endphp

        <a href="{{ route('admin.tours.index', ['status' => 'all']) }}"
           class="{{ $tabClasses }} {{ $status === 'all' ? $activeTab : $idleTab }}">
           Todos ({{ $counts['all'] }})
        </a>

        <a href="{{ route('admin.tours.index', ['status' => 'active']) }}"
           class="{{ $tabClasses }} {{ $status === 'active' ? $activeTab : $idleTab }}">
           Activos ({{ $counts['active'] }})
        </a>

        <a href="{{ route('admin.tours.index', ['status' => 'trashed']) }}"
           class="{{ $tabClasses }} {{ $status === 'trashed' ? $activeTab : $idleTab }}">
           Papelera ({{ $counts['trashed'] }})
        </a>
      </div>

      <a href="{{ route('admin.tours.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">
        Nuevo tour
      </a>
    </div>

    <div class="space-y-2">
      @forelse ($tours as $t)
        <div class="border rounded p-3 flex items-center justify-between {{ $t->trashed() ? 'opacity-70' : '' }}">
          <div>
            <div class="font-semibold">
              {{ $t->title }}
              @if($t->trashed())
                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Eliminado</span>
              @else
                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded">
                  {{ $t->is_active ? 'Activo' : 'Inactivo' }}
                </span>
              @endif
            </div>
            <div class="text-sm text-gray-600">
              {{ $t->destination }} · {{ $t->days }} días · ${{ number_format($t->base_price,0,',','.') }}
            </div>
          </div>

          <div class="flex items-center gap-2">
            {{-- Conservar el filtro al ir a Editar (opcional) --}}
            <a class="px-2 py-1 bg-gray-200 rounded"
               href="{{ route('admin.tours.edit', $t->id) }}?status={{ $status }}">
              Ver/Editar
            </a>

            @if(!$t->trashed())
              {{-- Enviar a papelera (soft delete) --}}
              <form method="POST" action="{{ route('admin.tours.destroy', $t->id) }}" class="inline">
                @csrf @method('DELETE')
                <button class="px-2 py-1 bg-red-600 text-white rounded"
                        onclick="return confirm('¿Enviar a papelera?')">Eliminar</button>
              </form>
            @else
              {{-- Restaurar (POST) --}}
              <form method="POST" action="{{ route('admin.tours.restore', $t->id) }}" class="inline">
                @csrf
                <button class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button>
              </form>

              {{-- Eliminar definitivamente (DELETE) --}}
              <form method="POST" action="{{ route('admin.tours.forceDelete', $t->id) }}" class="inline">
                @csrf @method('DELETE')
                <button class="px-2 py-1 bg-red-700 text-white rounded"
                        onclick="return confirm('Esto elimina definitivamente el tour y dependencias. ¿Continuar?')">
                  Eliminar definitivamente
                </button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <p>No hay tours en esta vista.</p>
      @endforelse
    </div>

    <div class="mt-4">
      {{-- Mantener el filtro en los links de paginación --}}
      {{ $tours->withQueryString()->links() }}
    </div>
  </div>
</x-app-layout>
