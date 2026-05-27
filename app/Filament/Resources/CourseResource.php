<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?int    $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('course_name')->label('Course Name')->required()->maxLength(150),
            TextInput::make('course_code')->label('Course Code')->maxLength(20),
            TextInput::make('term')->label('Term')->maxLength(25),
            TextInput::make('schedule_time')->label('Schedule')->maxLength(100),
            TextInput::make('course_price')->label('Price ($)')->numeric()->minValue(0)->nullable(),

            Select::make('inst_id')
                ->label('Instructor')
                ->relationship('instructor', 'ins_lname')
                ->getOptionLabelFromRecordUsing(fn ($r) => $r
                    ? trim(($r->ins_fname ?? '') . ' ' . ($r->ins_lname ?? ''))
                    : '—')
                ->searchable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course_name')->label('Name')->searchable()->sortable(),
                TextColumn::make('course_code')->label('Code')->toggleable(),
                TextColumn::make('term')->label('Term')->toggleable(),
                TextColumn::make('instructor.ins_lname')
                    ->label('Instructor')
                    ->formatStateUsing(fn ($state, $record) => $record->instructor
                        ? trim(($record->instructor->ins_fname ?? '') . ' ' . ($record->instructor->ins_lname ?? ''))
                        : '—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('instructor.organization.uni_name')->label('Organization')->toggleable(),
                TextColumn::make('course_price')->label('Price')->money('USD')->sortable(),
                TextColumn::make('testResults_count')->label('Assessments')->counts('testResults'),
            ])
            ->filters([
                SelectFilter::make('inst_id')
                    ->label('Instructor')
                    ->relationship('instructor', 'ins_lname')
                    ->getOptionLabelFromRecordUsing(fn ($r) => trim(($r->ins_fname ?? '') . ' ' . ($r->ins_lname ?? '')))
                    ->searchable(),
            ])
            ->actions([
                Action::make('configureSections')
                    ->label('Sections')
                    ->icon('heroicon-o-squares-2x2')
                    ->color('gray')
                    ->url(fn (Course $r) => static::getUrl('sections', ['record' => $r])),
                EditAction::make(),
            ])
            ->defaultSort('course_name');
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
