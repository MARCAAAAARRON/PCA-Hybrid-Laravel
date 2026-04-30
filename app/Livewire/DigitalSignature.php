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
                    ->disk('cloudinary')
                    ->directory('signatures')
                    ->acceptedFileTypes(['image/png', 'image/jpeg'])
                    ->maxSize(2048) // 2MB
                    ->imagePreviewHeight('80')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->extraInputAttributes(['capture' => 'environment'])
                    ->disabled(fn () => !$this->user->canUpdateSignature())
                    ->helperText(fn () => $this->user->canUpdateSignature()
                        ? 'Snap/Upload your signature. IMPORTANT: Click the PENCIL icon on the image to CROP it before saving.'
                        : 'You can only update your digital signature once every 3 months. Next update available: ' . $this->user->signature_updated_at->copy()->addMonths(3)->format('M d, Y')
                    )
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

        if (array_key_exists('signature_image', $data)) {
            if ($data['signature_image'] !== $this->user->signature_image) {
                $this->user->update([
                    'signature_image' => $data['signature_image'],
                    'signature_updated_at' => now(),
                ]);
            } else {
                $this->user->update([
                    'signature_image' => $data['signature_image'] ?? null,
                ]);
            }
        }

        Notification::make()
            ->success()
            ->title('Digital signature updated successfully.')
            ->send();
    }
}
