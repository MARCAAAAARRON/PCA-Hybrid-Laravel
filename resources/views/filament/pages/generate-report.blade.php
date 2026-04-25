<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="flex gap-4" style="padding-top: 4rem; padding-bottom: 2rem;">
            <x-filament::button type="submit" icon="heroicon-o-document-arrow-down" size="lg" class="shadow-xl" style="font-weight: bold; padding-left: 2rem; padding-right: 2rem; border-radius: 0.5rem;">
                Generate PDF Report
            </x-filament::button>

            <x-filament::button wire:click="exportExcel" type="button" color="success" icon="heroicon-o-document-chart-bar" size="lg" class="shadow-xl" style="font-weight: bold; padding-left: 2rem; padding-right: 2rem; border-radius: 0.5rem;">
                Download Excel Report
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
