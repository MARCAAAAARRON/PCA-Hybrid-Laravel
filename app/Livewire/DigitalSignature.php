<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class DigitalSignature extends MyProfileComponent
{
    protected string $view = 'livewire.digital-signature';

    public ?array $data = [];

    public $user;

    public static $sort = 15; // After PersonalInfo (10), before UpdatePassword (20)

    public function mount(): void
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->form->fill([
            'signature_image' => $this->user->signature_image,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('signature_image')
                    ->label('Digital Signature')
                    ->image()
                    ->disk('public')
                    ->directory('signatures')
                    ->acceptedFileTypes(['image/png', 'image/jpeg'])
                    ->maxSize(2048) // 2MB
                    ->imagePreviewHeight('80')
                    ->helperText('Upload a clear image of your signature (PNG with transparent background recommended).')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Delete old signature if replacing
        if ($this->user->signature_image && isset($data['signature_image']) && $data['signature_image'] !== $this->user->signature_image) {
            Storage::disk('public')->delete($this->user->signature_image);
        }

        $this->user->update([
            'signature_image' => $data['signature_image'] ?? null,
        ]);

        Notification::make()
            ->success()
            ->title('Digital signature updated successfully.')
            ->send();
    }
}
