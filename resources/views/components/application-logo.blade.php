@php
    $lightLogo = config('brand.logo_light', 'images/wg-logo-light.svg');
    $darkLogo = config('brand.logo_dark', 'images/wg-logo-dark.svg');
@endphp
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <img src="{{ asset($lightLogo) }}" alt="Welcome Group" class="h-10 w-auto dark:hidden">
    <img src="{{ asset($darkLogo) }}" alt="Welcome Group" class="hidden h-10 w-auto dark:block">
</div>
