<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Jeffgreco13\FilamentBreezy\Pages\MyProfilePage as BreezyProfilePage;

class MyProfile extends BreezyProfilePage
{
    protected static string $view = 'filament.pages.my-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        
        $this->form->fill([
            'first_name' => $user->first_name,
            'middle_initial' => $user->middle_initial,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'signature_image' => $user->signature_image,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required(),
                        TextInput::make('middle_initial')
                            ->label('M.I.')
                            ->maxLength(10),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required(),
                    ]),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique('users', 'email', ignorable: auth()->user()),
                
                FileUpload::make('signature_image')
                    ->label('Digital Signature')
                    ->image()
                    ->disk('public')
                    ->directory('signatures')
                    ->helperText('Upload a clear image of your signature (PNG with transparent background recommended).'),
                
                Section::make('Change Password')
                    ->description('Leave blank to keep current password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password')
                            ->currentPassword()
                            ->visible(fn() => filament('filament-breezy')->getPasswordUpdateRequiresCurrent()),
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->rule(Password::default()),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->same('new_password')
                            ->requiredWith('new_password'),
                    ])->compact(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $updateData = [
            'first_name' => $data['first_name'],
            'middle_initial' => $data['middle_initial'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'signature_image' => $data['signature_image'],
        ];

        if (!empty($data['new_password'])) {
            $updateData['password'] = Hash::make($data['new_password']);
        }

        $user->update($updateData);

        if (!empty($data['new_password'])) {
            $this->data['current_password'] = null;
            $this->data['new_password'] = null;
            $this->data['new_password_confirmation'] = null;
        }

        Notification::make()
            ->success()
            ->title('Profile updated successfully.')
            ->send();
    }
}
