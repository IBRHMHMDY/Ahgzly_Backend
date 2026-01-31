@php
    $tenant = \Filament\Facades\Filament::getTenant(); // Restaurant|null
@endphp

<div class="flex flex-col content-between items-center gap-3">
    {{-- @if($tenant?->getFilamentAvatarUrl())
        <img
            src="{{ $tenant->getFilamentAvatarUrl() }}"
            class="h-9 w-9 rounded-full object-fill"
            alt="Logo"
        />
    @endif --}}

    <div class="leading-tight">
        <div class="text-base font-semibold text-gray-900 dark:text-white">
            {{ config('app.name') }}
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $tenant?->name ?? 'No restaurant selected' }}
        </div>
    </div>
</div>
