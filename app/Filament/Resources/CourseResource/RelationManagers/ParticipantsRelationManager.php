<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Filament\Resources\ParticipantResource;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Read-only roster of participants enrolled in a course. Shows on the
 * CourseResource edit page so admins can see who has registered, how
 * many credits they have, and whether they have completed the assessment.
 *
 * Edits happen on the ParticipantResource directly (link out from each row).
 */
class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Enrolled Participants';

    protected static ?string $recordTitleAttribute = 'stud_lname';

    public function form(Schema $schema): Schema
    {
        // No inline create/edit — admins manage participants on their own
        // resource. Filament still requires a form() definition so we return
        // an empty schema.
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stud_lname')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stud_fname')
                    ->label('First Name')
                    ->searchable(),
                TextColumn::make('stud_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('stud_phone')
                    ->label('Phone')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('tot_credit')
                    ->label('Credits')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('testResults_count')
                    ->label('Assessments')
                    ->counts('testResults')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('has_credit')
                    ->label('Has credit')
                    ->query(fn (Builder $q) => $q->where('tot_credit', '>', 0)),
                Filter::make('took_assessment')
                    ->label('Took assessment')
                    ->query(fn (Builder $q) => $q->whereHas('testResults', fn ($q2) => $q2
                        ->whereNotNull('most_result_str'))),
            ])
            ->headerActions([])  // creation is handled on ParticipantResource
            ->actions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Participant $r) => ParticipantResource::getUrl('edit', ['record' => $r]))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No participants yet')
            ->emptyStateDescription('Participants who register for this course will appear here.')
            ->defaultSort('stud_lname');
    }
}
