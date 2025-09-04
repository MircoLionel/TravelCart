<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Administración de usuarios
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="GET" class="mb-4 flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar nombre, email o legajo"
                       class="border rounded px-3 py-2 w-full">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Buscar</button>
            </form>

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Rol</th>
                            <th class="px-4 py-2 text-left">Aprobado</th>
                            <th class="px-4 py-2 text-left">Legajo</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $u->id }}</td>
                                <td class="px-4 py-2">{{ $u->name }}</td>
                                <td class="px-4 py-2">{{ $u->email }}</td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('admin.users.update', $u) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <select name="role" class="border rounded px-2 py-1">
                                            <option value="buyer"  @selected($u->role==='buyer')>buyer</option>
                                            <option value="vendor" @selected($u->role==='vendor')>vendor</option>
                                            <option value="admin"  @selected($u->role==='admin')>admin</option>
                                        </select>
                                </td>
                                <td class="px-4 py-2">
                                        <select name="is_approved" class="border rounded px-2 py-1">
                                            <option value="1" @selected($u->is_approved)>Sí</option>
                                            <option value="0" @selected(!$u->is_approved)>No</option>
                                        </select>
                                </td>
                                <td class="px-4 py-2">
                                        <input name="legajo" value="{{ $u->legajo }}" class="border rounded px-2 py-1 w-32">
                                </td>
                                <td class="px-4 py-2">
                                        <button class="px-3 py-1 bg-indigo-600 text-white rounded">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-6" colspan="7">Sin resultados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
