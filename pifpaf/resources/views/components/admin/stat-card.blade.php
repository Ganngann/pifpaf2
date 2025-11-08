@props(['title', 'count', 'link', 'color' => 'blue', 'linkText'])

@php
    $base_classes = [
        'wrapper' => 'p-6 rounded-lg',
        'title'   => 'text-lg font-semibold',
        'count'   => 'text-3xl font-bold mt-2',
        'link'    => 'hover:underline mt-4 inline-block',
    ];

    $color_classes = [
        'blue' => [
            'wrapper' => 'bg-blue-100',
            'title'   => 'text-blue-800',
            'count'   => 'text-blue-900',
            'link'    => 'text-blue-600',
        ],
        'green' => [
            'wrapper' => 'bg-green-100',
            'title'   => 'text-green-800',
            'count'   => 'text-green-900',
            'link'    => 'text-green-600',
        ],
        'yellow' => [
            'wrapper' => 'bg-yellow-100',
            'title'   => 'text-yellow-800',
            'count'   => 'text-yellow-900',
            'link'    => 'text-yellow-600',
        ],
        'red' => [
            'wrapper' => 'bg-red-100',
            'title'   => 'text-red-800',
            'count'   => 'text-red-900',
            'link'    => 'text-red-600',
        ],
    ];

    $selected_color = $color_classes[$color] ?? $color_classes['blue'];
@endphp

<div class="{{ $base_classes['wrapper'] }} {{ $selected_color['wrapper'] }}">
    <h4 class="{{ $base_classes['title'] }} {{ $selected_color['title'] }}">{{ $title }}</h4>
    <p class="{{ $base_classes['count'] }} {{ $selected_color['count'] }}">{{ $count }}</p>
    <a href="{{ $link }}" class="{{ $base_classes['link'] }} {{ $selected_color['link'] }}">{{ $linkText }}</a>
</div>

<!--
Safelisting for Tailwind JIT compiler.
Do not remove this.

bg-blue-100
text-blue-800
text-blue-900
text-blue-600

bg-green-100
text-green-800
text-green-900
text-green-600

bg-yellow-100
text-yellow-800
text-yellow-900
text-yellow-600
-->
