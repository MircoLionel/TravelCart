<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form class="mb-4 flex gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, email o legajo"
                           class="w-full sm:w-80 border-gray-300 rounded-md">
                    <x-primary-button>Buscar</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full border divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">ID</th>
                                <th class="px-3 py-2 text-left">Nombre</th>
                                <th class="px-3 py-2 text-left">Email</th>
                                <th class="px-3 py-2 text-left">Legajo</th>
                                <th class="px-3 py-2 text-left">Rol</th>
                                <th class="px-3 py-2 text-left">Aprobado</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @forelse($users as $u)
                            <tr>
                                <td class="px-3 py-2">{{ $u->id }}</td>
                                <td class="px-3 py-2">{{ $u->name }}</td>
                                <td class="px-3 py-2">{{ $u->email }}</td>
                                <td class="px-3 py-2">{{ $u->legajo ?: '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded bg-gray-100">{{ $u->role ?? 'buyer' }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    @if($u->is_approved)
                                        <span class="text-green-600 font-semibold">Sí</span>
                                    @else
                                        <span class="text-gray-500">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="text-indigo-600 hover:underline">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-3 py-6 text-center text-gray-500" colspan="7">Sin resultados</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
