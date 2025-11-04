@props(['history'])

<div class="p-4 border rounded-lg flex items-center justify-between">
    <div class="flex-grow">
        <p class="font-semibold text-gray-800">{{ $history->description }}</p>
        <p class="text-sm text-gray-500">{{ $history->created_at->isoFormat('LLLL') }}</p>

        <div class="mt-2 sm:hidden">
            @if ($history->type === 'credit')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Crédit
                </span>
            @elseif ($history->type === 'debit')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    Débit
                </span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Retrait
                </span>
            @endif
        </div>
    </div>

    <div class="text-right">
        <p @class([
            'text-lg font-bold',
            'text-green-600' => $history->type === 'credit',
            'text-red-600' => $history->type !== 'credit',
        ])>
            {{ ($history->type === 'credit' ? '+' : '-') . number_format($history->amount, 2, ',', ' ') }} €
        </p>

        <div class="hidden sm:block mt-1">
             @if ($history->type === 'credit')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Crédit
                </span>
            @elseif ($history->type === 'debit')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    Débit
                </span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Retrait
                </span>
            @endif
        </div>
    </div>
</div>
