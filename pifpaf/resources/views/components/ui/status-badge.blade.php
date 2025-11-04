@props([
    'status'
])

@php
    $colorClasses = '';
    $text = '';

    switch ($status) {
        case \App\Enums\ItemStatus::AVAILABLE:
            $colorClasses = 'bg-green-100 text-green-800';
            $text = 'En ligne';
            break;
        case \App\Enums\ItemStatus::UNPUBLISHED:
            $colorClasses = 'bg-gray-100 text-gray-800';
            $text = 'Hors ligne';
            break;
        case \App\Enums\ItemStatus::SOLD:
            $colorClasses = 'bg-blue-100 text-blue-800';
            $text = 'Vendu';
            break;
        default:
            $colorClasses = 'bg-yellow-100 text-yellow-800';
            $text = 'Inconnu';
            break;
    }
@endphp

<span {{ $attributes->class(['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', $colorClasses]) }}>
    {{ $text }}
</span>
