@props([
    'alternateLabel' => null,
    'alternateHref' => null,
])

<div class="mt-6 pt-5 border-t border-moodhu-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
    <a href="{{ route('home') }}" class="text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150" wire:navigate>
        ← Back to home
    </a>
    @if($alternateLabel && $alternateHref)
        <a href="{{ $alternateHref }}" class="text-madi-600 hover:text-madi-700 font-medium transition-colors duration-150" wire:navigate>
            {{ $alternateLabel }}
        </a>
    @endif
</div>
