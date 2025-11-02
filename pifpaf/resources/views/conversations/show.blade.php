<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            @if($conversation->item && $conversation->item->primaryImage)
                <img src="{{ asset('storage/' . $conversation->item->primaryImage->path) }}" alt="{{ $conversation->item->title }}" class="w-12 h-12 rounded-md object-cover">
            @endif
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $conversation->item->title ?? 'Conversation' }}
                </h2>
                @php
                    $otherParticipant = $conversation->buyer_id == Auth::id() ? $conversation->seller : $conversation->buyer;
                @endphp
                <p class="text-sm text-gray-500">
                    Conversation avec {{ $otherParticipant->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Messages -->
                    <div class="space-y-4 mb-6">
                        @forelse($conversation->messages as $message)
                            <div class="flex {{ $message->user_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-lg px-4 py-2 rounded-lg {{ $message->user_id == Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    <p>{{ $message->content }}</p>
                                    <span class="text-xs {{ $message->user_id == Auth::id() ? 'text-blue-100' : 'text-gray-500' }} mt-1 block text-right">
                                        {{ $message->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Aucun message dans cette conversation. Soyez le premier à en envoyer un !</p>
                        @endforelse
                    </div>

                    <!-- Formulaire pour envoyer un message -->
                    <div class="mt-8 pt-6 border-t">
                        <form action="{{ route('messages.store', $conversation) }}" method="POST">
                            @csrf
                            <div class="flex items-center">
                                <textarea name="content" rows="2" class="w-full px-4 py-2 border rounded-l-md" placeholder="Écrivez votre message..." required></textarea>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-r-md">
                                    Envoyer
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
