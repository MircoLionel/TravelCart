<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usuarios
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('ok'))
                <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-2">
                    {{ session('ok') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded bg-red-50 text-red-800 px-4 py-2">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4">
                <form method="GET" class="flex gap-2">
                    <input name="q" value="{{ $q }}" placeholder="Buscar por nombre, email o legajo"
                           class="w-full rounded border-gray-300" />
                    <button class="rounded bg-gray-800 text-white px-3 py-2">Buscar</button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 border-b">
                            <tr>
                                <th class="py-2">ID</th>
                                <th class="py-2">Nombre</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Legajo</th>
                                <th class="py-2">Rol</th>
                                <th class="py-2">Admin</th>
                                <th class="py-2">Aprobado</th>
                                <th class="py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $u)
                            <tr class="border-b last:border-0">
                                <td class="py-2">{{ $u->id }}</td>
                                <td class="py-2">
                                    <div class="font-medium">{{ $u->name }}</div>
                                    <div class="text-xs text-gray-500">Creado: {{ $u->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-2">{{ $u->email }}</td>

                                <td class="py-2">
                                    <form method="POST" action="{{ route('admin.users.update',$u) }}" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="legajo" value="{{ old('legajo', $u->legajo) }}"
                                               class="rounded border-gray-300 w-32" placeholder="Legajo">

                                        <select name="role" class="rounded border-gray-300">
                                            <option value="buyer"    @selected($u->role === 'buyer')>Cliente</option>
                                            <option value="supplier" @selected($u->role === 'supplier')>Proveedor</option>
                                        </select>

                                        <label class="inline-flex items-center gap-1 text-sm">
                                            <input type="checkbox" name="is_admin" value="1" @checked($u->is_admin)>
                                            <span>Admin</span>
                                        </label>

                                        <button class="ml-auto rounded bg-indigo-600 text-white px-3 py-1">Guardar</button>
                                    </form>
                                </td>

                                <td class="py-2"></td>
                                <td class="py-2"></td>

                                <td class="py-2">
                                    @if($u->is_approved)
                                        <span class="inline-flex items-center rounded bg-green-100 text-green-800 px-2 py-0.5 text-xs">Aprobado</span>
                                    @else
                                        <span class="inline-flex items-center rounded bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs">Pendiente</span>
                                    @endif
                                </td>

                                <td class="py-2 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(!$u->is_approved)
                                            <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                                @csrf
                                                <button class="rounded bg-emerald-600 text-white px-3 py-1"
                                                    @disabled(!$u->legajo)>
                                                    Aprobar
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.revoke', $u) }}">
                                                @csrf
                                                <button class="rounded bg-rose-600 text-white px-3 py-1">Revocar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-gray-500">No hay usuarios.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-4 py-2 border-t">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
