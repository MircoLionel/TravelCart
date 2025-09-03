<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar usuario #{{ $user->id }} — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('ok'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('ok') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label value="Nombre" />
                        <x-text-input class="block mt-1 w-full" value="{{ $user->name }}" disabled />
                    </div>

                    <div>
                        <x-input-label value="Email" />
                        <x-text-input class="block mt-1 w-full" value="{{ $user->email }}" disabled />
                    </div>

                    <div>
                        <x-input-label for="legajo" value="Legajo" />
                        <x-text-input id="legajo" name="legajo" class="block mt-1 w-full"
                                      value="{{ old('legajo', $user->legajo) }}" />
                        <x-input-error :messages="$errors->get('legajo')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Rol" />
                        <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md">
                            @foreach(['admin'=>'Admin','vendor'=>'Vendedor','buyer'=>'Comprador'] as $value=>$label)
                                <option value="{{ $value }}" @selected(old('role',$user->role ?? 'buyer') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="is_approved" type="checkbox" name="is_approved" value="1"
                               @checked(old('is_approved', $user->is_approved))>
                        <x-input-label for="is_approved" value="Aprobado para operar" />
                        <x-input-error :messages="$errors->get('is_approved')" class="mt-2" />
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <x-primary-button>Guardar</x-primary-button>
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
