<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @forelse ($notifications as $notification)
                        <div class="flex justify-between items-center p-4 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                            <div class="flex-grow">
                                <a href="{{ route('notifications.read-and-redirect', $notification->id) }}" class="{{ $notification->unread() ? 'font-bold' : '' }}">
                                    {{ $notification->data['message'] }}
                                </a>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @if ($notification->unread())
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="ml-4 flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-blue-500 hover:text-blue-700">Marquer comme lu</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p>Vous n'avez aucune notification.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
