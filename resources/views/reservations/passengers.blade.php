<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Pasajeros para {{ $reservation->locator }}</h1>
            <span class="text-sm text-gray-600">Tiempo límite: {{ optional($reservation->hold_expires_at)->format('H:i') }}</span>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 space-y-4">
        @if(session('ok'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                <p class="font-semibold">Revisá los datos de los pasajeros:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('reservations.passengers.store', $reservation) }}" class="space-y-4">
            @csrf
            <p class="text-sm text-gray-600">Cargá los {{ $reservation->qty }} pasajeros en menos de 10 minutos para mantener la reserva.</p>
            @for($i = 0; $i < $reservation->qty; $i++)
                @php $existing = $reservation->passengers[$i] ?? null; @endphp
                <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-2">
                    <h3 class="text-sm font-semibold text-gray-800">Pasajero {{ $i+1 }}</h3>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="space-y-1">
                            <input type="text" name="passengers[{{ $i }}][first_name]" placeholder="Nombre" value="{{ old("passengers.$i.first_name", optional($existing)->first_name) }}" class="w-full rounded border-gray-300" required>
                            @error("passengers.$i.first_name")
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <input type="text" name="passengers[{{ $i }}][last_name]" placeholder="Apellido" value="{{ old("passengers.$i.last_name", optional($existing)->last_name) }}" class="w-full rounded border-gray-300" required>
                            @error("passengers.$i.last_name")
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <input type="text" name="passengers[{{ $i }}][document_number]" placeholder="Documento" value="{{ old("passengers.$i.document_number", optional($existing)->document_number) }}" class="w-full rounded border-gray-300" required>
                            @error("passengers.$i.document_number")
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <input type="date" name="passengers[{{ $i }}][birth_date]" value="{{ old("passengers.$i.birth_date", optional(optional($existing)->birth_date)->format('Y-m-d')) }}" class="w-full rounded border-gray-300">
                            @error("passengers.$i.birth_date")
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <select name="passengers[{{ $i }}][sex]" class="w-full rounded border-gray-300">
                            <option value="">Sexo</option>
                            <option value="M" @selected(old("passengers.$i.sex", optional($existing)->sex) === 'M')>M</option>
                            <option value="F" @selected(old("passengers.$i.sex", optional($existing)->sex) === 'F')>F</option>
                            <option value="X" @selected(old("passengers.$i.sex", optional($existing)->sex) === 'X')>X</option>
                            </select>
                            @error("passengers.$i.sex")
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endfor
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-white" type="submit">Guardar pasajeros</button>
        </form>
    </div>
</x-app-layout>
