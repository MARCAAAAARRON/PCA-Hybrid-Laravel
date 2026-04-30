<x-filament-widgets::widget>
    <x-filament::section shadow="sm" border="true">
        <div class="flex items-center gap-2 mb-6">
            <x-filament::icon
                icon="heroicon-m-bolt"
                class="h-5 w-5 text-gray-500"
            />
            <h2 class="text-base font-bold leading-6 text-gray-950 dark:text-white">
                Quick Actions
            </h2>
        </div>

        <div class="flex flex-col gap-3">
            <x-filament::button
                href="{{ \App\Filament\Resources\UserResource::getUrl('create') }}"
                tag="a"
                color="gray"
                outlined
                icon="heroicon-m-user-plus"
                class="justify-start w-full text-left"
            >
                Create New User Account
            </x-filament::button>

            <x-filament::button
                href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}"
                tag="a"
                color="gray"
                outlined
                icon="heroicon-m-users"
                class="justify-start w-full text-left"
            >
                Manage User Accounts
            </x-filament::button>

            <x-filament::button
                href="{{ \App\Filament\Resources\FieldSiteResource::getUrl('index') }}"
                tag="a"
                color="gray"
                outlined
                icon="heroicon-m-map-pin"
                class="justify-start w-full text-left"
            >
                Manage Field Sites
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
