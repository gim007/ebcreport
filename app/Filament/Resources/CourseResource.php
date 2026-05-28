<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?int    $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Course Information')
                ->columns(12)
                ->schema([
                    TextInput::make('course_name')
                        ->label('Course Name')
                        ->required()
                        ->maxLength(150)
                        ->columnSpan(8),
                    TextInput::make('course_code')
                        ->label('Course Code')
                        ->placeholder('e.g. MGT 589')
                        ->maxLength(20)
                        ->columnSpan(4),
                    TextInput::make('term')
                        ->label('Term')
                        ->placeholder('e.g. Spring 2026')
                        ->maxLength(25)
                        ->columnSpan(6),
                    TextInput::make('schedule_time')
                        ->label('Schedule')
                        ->placeholder('e.g. Tue/Thu 1:30 PM – 3:00 PM')
                        ->maxLength(100)
                        ->columnSpan(6),
                ]),

            Section::make('Instructor')
                ->description('The instructor responsible for the course. Their organization determines which report sections apply.')
                ->schema([
                    Select::make('inst_id')
                        ->label('Instructor')
                        // Plain options() instead of relationship() — Filament's
                        // relationship Select uses `find()` against the related
                        // model's default key which can break on legacy schemas
                        // where the PK isn't `id`. Building the options manually
                        // is safer and lets us show "Name — Organization" in
                        // one line for disambiguation.
                        ->options(function () {
                            return \App\Models\Instructor::query()
                                ->with('organization')
                                ->orderBy('ins_lname')
                                ->orderBy('ins_fname')
                                ->get()
                                ->mapWithKeys(fn ($i) => [
                                    $i->ins_id => trim(($i->ins_fname ?? '') . ' ' . ($i->ins_lname ?? ''))
                                        . ($i->organization?->uni_name ? " — {$i->organization->uni_name}" : ''),
                                ])
                                ->all();
                        })
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Pricing & Availability')
                ->columns(12)
                ->schema([
                    TextInput::make('course_price')
                        ->label('Price')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->prefix('$')
                        ->nullable()
                        ->columnSpan(4),
                    DatePicker::make('expiry_date')
                        ->label('Expires on')
                        ->helperText('Past-expiry courses don\'t appear in participant selection. Leave blank for no expiry.')
                        ->native(false)
                        ->displayFormat('M j, Y')
                        ->columnSpan(4),
                    Toggle::make('is_hidden')
                        ->label('Hidden from participants')
                        ->helperText('Hidden courses stay in admin but don\'t appear in the public selection grid.')
                        ->columnSpan(4),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('course_code')->label('Code')->toggleable(),
                TextColumn::make('term')->label('Term')->toggleable(),
                TextColumn::make('instructor.ins_lname')
                    ->label('Instructor')
                    ->formatStateUsing(fn ($state, $record) => $record->instructor
                        ? trim(($record->instructor->ins_fname ?? '') . ' ' . ($record->instructor->ins_lname ?? ''))
                        : '—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('instructor.organization.uni_name')
                    ->label('Organization')
                    ->toggleable(),
                TextColumn::make('course_price')->label('Price')->money('USD')->sortable(),
                TextColumn::make('expiry_date')
                    ->label('Expires')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),
                IconColumn::make('is_hidden')
                    ->label('Hidden')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('success'),
                TextColumn::make('testResults_count')
                    ->label('Assessments')
                    ->counts('testResults')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('inst_id')
                    ->label('Instructor')
                    ->relationship('instructor', 'ins_lname')
                    ->getOptionLabelFromRecordUsing(fn ($r) => trim(($r->ins_fname ?? '') . ' ' . ($r->ins_lname ?? '')))
                    ->searchable(),
                TernaryFilter::make('is_hidden')->label('Hidden'),
                Filter::make('expired')
                    ->label('Expired only')
                    ->query(fn (Builder $q) => $q->whereNotNull('expiry_date')->where('expiry_date', '<', now())),
            ])
            ->actions([
                Action::make('configureSections')
                    ->label('Sections')
                    ->icon('heroicon-o-squares-2x2')
                    ->color('gray')
                    ->url(fn (Course $r) => static::getUrl('sections', ['record' => $r])),
                Action::make('toggleHidden')
                    ->label(fn (Course $r) => $r->is_hidden ? 'Show' : 'Hide')
                    ->icon('heroicon-o-eye-slash')
                    ->action(fn (Course $r) => $r->update(['is_hidden' => ! $r->is_hidden])),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('course_name');
    }

    public static function getRelations(): array
    {
        return [
            CourseResource\RelationManagers\ParticipantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'    => Pages\ListCourses::route('/'),
            'edit'     => Pages\EditCourse::route('/{record}/edit'),
            'sections' => Pages\ConfigureCourseSections::route('/{record}/sections'),
        ];
    }
}
