@props(['user'])

@if ($user)
    <a {{ $attributes->merge(['class' => 'font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out']) }}
       href="{{ route('profile.show', $user) }}">
        {{ $user->name }}
    </a>
@endif
