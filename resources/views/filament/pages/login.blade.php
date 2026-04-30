<div>
    <div class="pca-bg-circles">
        <div class="circle-top-right"></div>
        <div class="circle-bottom-left"></div>
    </div>

    <x-filament-panels::page.simple>
        <x-slot name="heading">
            <h1 class="fi-simple-header-heading text-4xl font-black tracking-tight text-gray-900 dark:text-white">
                Sign in
            </h1>
        </x-slot>

        @if (filament()->hasRegistration())
            <x-slot name="subheading">
                {{ __('filament-panels::pages/auth/login.actions.register.before') }}

                {{ $this->registerAction }}
            </x-slot>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <x-filament-panels::form id="form" wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
        </x-filament-panels::form>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
    </x-filament-panels::page.simple>
</div>
