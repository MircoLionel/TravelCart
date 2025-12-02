<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Proveedores
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" class="mb-4 flex flex-col gap-2 md:flex-row">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, email o legajo"
                       class="border rounded px-3 py-2 w-full md:w-96">
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
                            <th class="px-4 py-2 text-left">Legajo</th>
                            <th class="px-4 py-2 text-left">Aprobado</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $vendor->id }}</td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('admin.vendors.update', $vendor) }}" method="POST" class="space-y-2 md:space-y-0 md:flex md:items-center md:gap-3">
                                        @csrf
                                        @method('PATCH')

                                        <input name="name" value="{{ $vendor->name }}" class="border rounded px-2 py-1 w-full md:w-48" placeholder="Nombre">
                                </td>
                                <td class="px-4 py-2">
                                        <input name="email" value="{{ $vendor->email }}" class="border rounded px-2 py-1 w-full md:w-64" placeholder="Email">
                                </td>
                                <td class="px-4 py-2">
                                        <input name="legajo" value="{{ $vendor->legajo }}" class="border rounded px-2 py-1 w-32" placeholder="Legajo">
                                </td>
                                <td class="px-4 py-2">
                                        <select name="is_approved" class="border rounded px-2 py-1">
                                            <option value="1" @selected($vendor->is_approved)>Sí</option>
                                            <option value="0" @selected(!$vendor->is_approved)>No</option>
                                        </select>
                                </td>
                                <td class="px-4 py-2 text-right">
                                        <button class="px-3 py-1 bg-indigo-600 text-white rounded">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-6" colspan="6">No hay proveedores.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
