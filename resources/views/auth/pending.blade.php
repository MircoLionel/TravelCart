<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        @if(session('error'))
            <div class="mb-4 rounded bg-yellow-100 border border-yellow-300 text-yellow-900 px-4 py-2">
                {{ session('error') }}
            </div>
        @endif

        <h1 class="text-2xl font-semibold mb-2">Tu cuenta está pendiente de aprobación</h1>
        <p>Un administrador revisará tu solicitud. Te avisaremos por correo cuando puedas operar normalmente.</p>
    </div>
</x-app-layout>
