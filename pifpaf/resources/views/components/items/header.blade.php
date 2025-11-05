@props(['item'])

<div>
    {{-- Titre du produit --}}
    <h1 class="text-3xl font-bold leading-tight" dusk="item-title">{{ $item->title }}</h1>

    {{-- Description courte ou slogan --}}
    <p class="mt-2 text-lg text-gray-600">{{ $item->short_description ?? 'Un article de qualité à ne pas manquer.' }}</p>

    <div class="mt-4 text-sm text-gray-500">
        <div class="flex items-center">
            <span>Vendu par :</span>
            <a href="{{ route('profile.show', $item->user) }}" class="ml-1 font-semibold text-blue-600 hover:underline">{{ $item->user->name }}</a>
        </div>
        <div class="mt-1">
            <span>Publié le :</span>
            <span class="ml-1">{{ $item->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
</div>
