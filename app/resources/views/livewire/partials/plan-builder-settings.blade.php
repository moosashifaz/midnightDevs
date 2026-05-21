<div class="grid sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <x-ui.budget-controls
            :presets="[500, 2000, 5000]"
            :disabled="$isGenerating"
        />
    </div>
    <div class="sm:col-span-2 sm:max-w-xs">
        <label for="plan-days" class="block text-xs font-semibold uppercase tracking-label text-muraka-500 mb-1">Days</label>
        <select id="plan-days" wire:model="days" class="input-field w-full" @disabled($isGenerating)>
            @foreach([3, 5, 7] as $d)
                <option value="{{ $d }}">{{ $d }} days</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <x-ui.planner-interests :interests="$interests" :disabled="$isGenerating" />
    </div>
</div>
