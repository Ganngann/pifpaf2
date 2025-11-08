<div {{ $attributes->merge(['class' => 'text-center border-2 border-dashed border-gray-300 p-12 rounded-lg']) }}>
    @if (isset($icon))
        <div class="mx-auto h-12 w-12 text-gray-400">
            {{ $icon }}
        </div>
    @endif

    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $slot }}</h3>

    @if (isset($description))
        <p class="mt-1 text-sm text-gray-500">
            {{ $description }}
        </p>
    @endif


    @if (isset($actions))
        <div class="mt-6">
            {{ $actions }}
        </div>
    @endif
</div>
