<div>
    <div class="pca-bg-circles">
        <div class="circle-top-right"></div>
        <div class="circle-bottom-left"></div>
    </div>

    <x-filament-panels::page.simple>
        <x-slot name="heading">
            <a href="/"
                style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; font-weight: 600; color: #10b981; text-decoration: none; margin-bottom: 0.5rem; transition: color 0.2s;"
                onmouseover="this.style.color='#059669'"
                onmouseout="this.style.color='#10b981'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1rem; height: 1rem;">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                Back to Home
            </a>
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
