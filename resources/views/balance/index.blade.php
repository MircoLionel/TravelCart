<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Balance</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Resumen de ingresos por mes.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-gray-800 dark:bg-gray-900/80">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Balance mensual</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Comparativo de los últimos 6 meses.</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                        Ingresos
                    </div>
                </div>

                @php
                    $monthlyBalance = [
                        ['label' => 'Abr', 'value' => 3800],
                        ['label' => 'May', 'value' => 5200],
                        ['label' => 'Jun', 'value' => 4600],
                        ['label' => 'Jul', 'value' => 6100],
                        ['label' => 'Ago', 'value' => 7400],
                        ['label' => 'Sep', 'value' => 6900],
                    ];
                    $maxBalance = collect($monthlyBalance)->max('value') ?: 1;
                @endphp

                <div class="mt-8 grid grid-cols-6 items-end gap-4">
                    @foreach ($monthlyBalance as $month)
                        @php
                            $height = max(12, ($month['value'] / $maxBalance) * 160);
                        @endphp
                        <div class="flex flex-col items-center gap-3">
                            <div class="relative flex h-44 w-full items-end justify-center rounded-xl bg-gray-100/80 p-2 dark:bg-gray-800/60">
                                <div
                                    class="w-full rounded-lg bg-gradient-to-t from-indigo-500 via-indigo-400 to-sky-400 shadow-md"
                                    style="height: {{ $height }}px"
                                    role="img"
                                    aria-label="Ingreso {{ $month['label'] }}: ${{ number_format($month['value'], 0, ',', '.') }}"
                                ></div>
                            </div>
                            <div class="text-center">
                                <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $month['label'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($month['value'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
