@props([
    'disabled' => false,
    'alpine' => false,
])

@php
    $options = \App\Services\Planner\PlanInterests::OPTIONS;
@endphp

<fieldset class="space-y-2" @if($alpine) :aria-disabled="!budgetChosen" @endif @disabled($disabled)>
    <legend class="text-[10px] uppercase tracking-label text-muraka-500 font-semibold">
        What are you into?
    </legend>
    <p class="text-xs text-muraka-500 -mt-1">
        Tick activities you want — we'll filter your plan to snorkeling, food, crafts, and more.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @foreach($options as $key => $option)
            @if($alpine)
                <button
                    type="button"
                    role="checkbox"
                    :aria-checked="interests['{{ $key }}'] ? 'true' : 'false'"
                    @click="interests['{{ $key }}'] = !interests['{{ $key }}']"
                    :class="interests['{{ $key }}'] ? 'planner-interest-active' : 'planner-interest'"
                    class="text-left"
                >
                    <span class="planner-interest-check" aria-hidden="true">
                        <x-icons.icon name="check" class="w-3.5 h-3.5" x-show="interests['{{ $key }}']" x-cloak />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2 font-semibold text-sm text-muraka-900">
                            <x-icons.icon :name="$option['icon']" class="w-4 h-4 shrink-0 text-madi-600" />
                            {{ $option['label'] }}
                        </span>
                        <span class="block text-[11px] text-muraka-500 mt-0.5">{{ $option['hint'] }}</span>
                    </span>
                </button>
            @else
                <label @class([
                    'planner-interest cursor-pointer',
                    'planner-interest-active' => in_array($key, $interests ?? [], true),
                    'opacity-60 pointer-events-none' => $disabled,
                ])>
                    <input
                        type="checkbox"
                        class="sr-only"
                        value="{{ $key }}"
                        wire:model.live="interests"
                        @disabled($disabled)
                    />
                    <span class="planner-interest-check">
                        @if(in_array($key, $interests ?? [], true))
                            <x-icons.icon name="check" class="w-3.5 h-3.5" />
                        @endif
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2 font-semibold text-sm text-muraka-900">
                            <x-icons.icon :name="$option['icon']" class="w-4 h-4 shrink-0 text-madi-600" />
                            {{ $option['label'] }}
                        </span>
                        <span class="block text-[11px] text-muraka-500 mt-0.5">{{ $option['hint'] }}</span>
                    </span>
                </label>
            @endif
        @endforeach
    </div>

    @if($alpine)
        <p class="text-xs text-red-600" x-show="budgetChosen && selectedInterests().length === 0" x-cloak>
            Pick at least one activity to continue.
        </p>
    @else
        @error('interests')
            <p class="text-xs text-red-600">{{ $message }}</p>
        @enderror
    @endif
</fieldset>
