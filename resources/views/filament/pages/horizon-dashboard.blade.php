<x-filament-panels::page>
    <form wire:submit="launch" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Launch Horizon
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
