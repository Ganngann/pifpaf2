@props(['item'])

<h1 class="text-4xl font-bold mb-2" dusk="item-title">{{ $item->title }}</h1>
<div class="mb-6">
    <div>
        <span class="text-gray-500">Vendu par :</span>
        <x-ui.user-profile-link :user="$item->user" class="font-semibold text-blue-600 hover:underline" />
    </div>
    <div class="mt-2 text-sm text-gray-500">
        Publié le {{ $item->created_at->format('d/m/Y') }}
    </div>
</div>
