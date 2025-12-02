<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <img src="{{ asset('images/wg-logo-dark.svg') }}" alt="Welcome Group" class="h-10 w-auto dark:hidden">
    <img src="{{ asset('images/wg-logo-light.svg') }}" alt="Welcome Group" class="hidden h-10 w-auto dark:block">
</div>
