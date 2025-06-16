<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Notifications\IdVerificationApproved;
use App\Notifications\IdVerificationDeclined;
use App\Notifications\Project\ProjectApproved;
use App\Notifications\Project\ProjectDeclined;
use App\Enum\Project\Status; // Import the Project Status Enum
use Illuminate\Support\Carbon; // Import Carbon for date/time
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect; // Import Redirect facade

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    public function mount($record): void
    {
        parent::mount($record);
        $ticket = $this->record;
        if ($ticket->assigned_to !== Auth::id()) {
            abort(403, 'You are not authorized to edit this ticket.');
        }

        if ($ticket->status !== "open") {
            abort(403, "This ticket is {$ticket->status} by {$ticket->assigned->name} and you can't edit it anymore.");
        }
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        switch ($record->category->slug) {
            case "project-approval":
                return $this->projectApproval();
                break;
            case 'user-verification':
            case 'company-verification':
            case 'address-verification':
                return $this->verification();
        }
        return [];
    }

    private function projectApproval(): array
    {
        return [
            // Accept Project Action
            Actions\Action::make('acceptProject')
                ->label('Accept Project')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Accept Project?')
                ->modalDescription('Are you sure you want to accept this project? The project will be published and the ticket closed.')
                ->action(function () {
                    $record = $this->getRecord();
                    $project = $record->project;

                    if ($project) {
                        $project->update([
                            'status' => Status::Published,
                            'published_at' => Carbon::now(),
                        ]);
                        if ($project->owner) { // Changed from client to owner for consistency with previous discussion
                            $project->owner->notify(new ProjectApproved($project));
                        }
                    }
                    $record->update(['status' => 'closed']);

                    \Filament\Notifications\Notification::make()
                        ->title('Project Accepted')
                        ->success()
                        ->send();
                })
                ->after(fn() => Redirect::to(TicketResource::getUrl('index'))), // Redirect after accept

            // Deny Project Action
            Actions\Action::make('denyProject')
                ->label('Deny Project')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Deny Project?')
                ->modalDescription('Are you sure you want to deny this project? The project will be set to draft and the owner will be notified.')
                ->action(function () {
                    $record = $this->getRecord();
                    $project = $record->project;

                    if ($project) {
                        $project->update([
                            'status' => Status::Draft,
                        ]);
                        if ($project->owner) { // Changed from client to owner for consistency with previous discussion
                            $project->owner->notify(new ProjectDeclined($project));
                        }
                    }
                    $record->update(['status' => 'closed']);

                    \Filament\Notifications\Notification::make()
                        ->title('Project Denied')
                        ->success() // Consider changing to 'danger' or 'warning'
                        ->send();
                })
                ->after(fn() => Redirect::to(TicketResource::getUrl('index'))), // Redirect after deny
        ];
    }

    private function verification(): array
    {
        return [

            // Accept Verification Action
            Actions\Action::make('accept')
                ->label('Accept Verification')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Accept Verification?')
                ->modalDescription('Are you sure you want to accept this verification?')
                ->action(function () {
                    $record = $this->getRecord();
                    $verification = $record->verification;
                    if ($verification) {
                        $verification->update([
                            'verified' => true,
                        ]);
                        if ($verification->user) {
                            $verification->user->notify(new IdVerificationApproved($verification));
                        }
                    }
                    $record->update(['status' => 'closed']);

                    \Filament\Notifications\Notification::make()
                        ->title('Verification Accepted')
                        ->success()
                        ->send();
                })
                ->after(fn() => Redirect::to(TicketResource::getUrl('index'))), // Redirect after accept

            // Deny Verification Action
            Actions\Action::make('deny')
                ->label('Deny')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Deny Verification?')
                ->modalDescription('Are you sure you want to deny this verification? The verification will be marked as not verified and the user will be notified.')
                ->action(function () {
                    $record = $this->getRecord();
                    $verification = $record->verification;
                    if ($verification) {
                        $verification->update([
                            'verified' => false,
                        ]);
                        if ($verification->user) {
                            $verification->user->notify(new IdVerificationDeclined($verification));
                        }
                    }
                    $record->update(['status' => 'closed']);

                    \Filament\Notifications\Notification::make()
                        ->title('Verification Denied')
                        ->success()
                        ->send();
                })
                ->after(fn() => Redirect::to(TicketResource::getUrl('index'))), // Redirect after deny
        ];
    }
}
