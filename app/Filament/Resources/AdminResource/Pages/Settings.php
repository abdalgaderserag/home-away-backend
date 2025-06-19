<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException; // Import this

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationGroup = 'Settings'; // Optional: organize in a group
    protected static ?int $navigationSort = 1; // Optional: set order

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(auth()->user()->only(['email', 'name']));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique('users', 'email', ignoreRecord: true),
                TextInput::make('current_password')
                    ->password()
                    ->label('Current Password (Required to change email/password)')
                    ->requiredWithoutAll('password', 'email')
                    ->autofocus(false),
                TextInput::make('password')
                    ->password()
                    ->label('New Password')
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(false)
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Confirm New Password')
                    ->required(false)
                    ->visible(fn($get) => filled($get('password'))),
            ])
            ->statePath('data')
            ->model(auth()->user());
    }

    public function submit(): void
    {
        try {
            $data = $this->form->getState();
        } catch (ValidationException $e) {
            throw $e;
        }

        $user = auth()->user();

        $changingPassword = filled($data['password']);
        $changingEmail = $data['email'] !== $user->email;

        if (($changingPassword || $changingEmail) && ! Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Incorrect current password')
                ->danger()
                ->send();
            return;
        }

        $updateData = [];
        if ($data['name'] !== $user->name) {
            $updateData['name'] = $data['name'];
        }
        if ($changingEmail) {
            $updateData['email'] = $data['email'];
            $updateData['email_verified_at'] = null;
        }
        if ($changingPassword) {
            $updateData['password'] = $data['password'];
        }

        if (!empty($updateData)) {
            $user->update($updateData);

            Notification::make()
                ->title('Profile updated successfully!')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('No changes detected.')
                ->warning()
                ->send();
        }
    }
}
