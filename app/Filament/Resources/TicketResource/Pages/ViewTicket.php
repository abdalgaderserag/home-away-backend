<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Project;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getFormSchema(): array
    {
        // Base ticket fields from parent schema
        $schema = parent::getFormSchema();

        // Only show project section for 'Project Approval' tickets
        if ($this->record->category?->name === 'Project Approval') {
            // Attempt to load related project
            $project = Project::find($this->record->project_id);

            if ($project) {
                $schema[] = Forms\Components\Section::make('Project Details')
                    ->schema([
                        Forms\Components\TextInput::make('project_title')
                            ->label('Title')
                            ->disabled()
                            ->default($project->title),

                        Forms\Components\Textarea::make('project_description')
                            ->label('Description')
                            ->disabled()
                            ->default($project->description),

                        Forms\Components\TextInput::make('project_status')
                            ->label('Status')
                            ->disabled()
                            ->default((string)$project->status),

                        Forms\Components\TextInput::make('project_deadline')
                            ->label('Deadline')
                            ->disabled()
                            ->default($project->deadline->toDateString()),
                    ]);
            }
        }

        return $schema;
    }
}
