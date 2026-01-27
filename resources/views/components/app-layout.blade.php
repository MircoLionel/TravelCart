@php($headerSlot = $header ?? null)

@include('layouts.app', [
    'header' => $headerSlot,
    'slot' => $slot,
])
