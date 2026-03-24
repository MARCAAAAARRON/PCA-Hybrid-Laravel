<x-filament::section
    icon="heroicon-o-pencil-square"
    icon-color="primary"
    aside
>
    <x-slot name="heading">
        Digital Signature
    </x-slot>

    <x-slot name="description">
        Upload your digital signature image. This will appear on generated reports as your official signature.
    </x-slot>

    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        @if($this->user->signature_image)
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Current Signature:</p>
                <img
                    src="{{ Storage::disk('public')->url($this->user->signature_image) }}"
                    alt="Digital Signature"
                    class="max-h-16 object-contain"
                >
            </div>
        @endif

        <div class="text-right">
            <x-filament::button type="submit">
                Save Signature
            </x-filament::button>
        </div>
    </form>
</x-filament::section>
