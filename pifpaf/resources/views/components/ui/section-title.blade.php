@props(['level' => 2, 'class' => ''])

@php
$tag = 'h' . $level;
$defaultClasses = 'text-2xl font-bold mb-6 text-center sm:text-left';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $defaultClasses . ' ' . $class]) }}>
    {{ $slot }}
</{{ $tag }}>
