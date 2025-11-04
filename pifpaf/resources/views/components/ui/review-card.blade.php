@props(['review'])

<div class="py-4 border-b last:border-b-0">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            {{-- Placeholder for user avatar, can be added later --}}
            <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                <span class="text-lg font-semibold text-gray-500">{{ strtoupper(substr($review->reviewer->name, 0, 1)) }}</span>
            </div>
        </div>
        <div class="ml-4 flex-grow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-semibold text-gray-800">{{ $review->reviewer->name }}</span>
                    <p class="text-xs text-gray-500">{{ $review->created_at->isoFormat('LL') }}</p>
                </div>
                <div class="flex items-center">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg @class([
                            'h-5 w-5',
                            'text-yellow-400' => $i <= $review->rating,
                            'text-gray-300' => $i > $review->rating,
                        ]) fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    @endfor
                </div>
            </div>

            <p class="text-gray-700 mt-2">{{ $review->comment }}</p>
        </div>
    </div>
</div>
