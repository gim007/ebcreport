<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Participants';
    protected static ?int    $navigationSort = 3;

    private const COUNTRIES = [
        'US' => 'United States', 'CA' => 'Canada', 'GB' => 'United Kingdom',
        'AU' => 'Australia', 'IN' => 'India', 'DE' => 'Germany', 'FR' => 'France',
        'IE' => 'Ireland', 'NZ' => 'New Zealand', 'ZA' => 'South Africa',
        'MX' => 'Mexico', 'BR' => 'Brazil', 'JP' => 'Japan',
        'SG' => 'Singapore', 'NL' => 'Netherlands', 'ES' => 'Spain',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('stud_fname')->label('First Name')->required()->maxLength(100),
                    TextInput::make('stud_lname')->label('Last Name')->required()->maxLength(100),
                    Select::make('stud_gender')->label('Gender')->options([
                        'Male' => 'Male', 'Female' => 'Female',
                        'Other' => 'Other', 'Prefer not to say' => 'Prefer not to say',
                    ]),
                ]),

            Section::make('Contact')
                ->description('Phone is required for SMS recovery (R-31).')
                ->columns(2)
                ->schema([
                    TextInput::make('stud_email')->label('Email')->email()->nullable(),
                    TextInput::make('stud_phone')->label('Phone')->tel()->maxLength(50),
                ]),

            Section::make('Enrollment')
                ->columns(3)
                ->schema([
                    Select::make('inst_id')
                        ->label('Instructor')
                        ->relationship('instructor', 'ins_lname')
                        ->getOptionLabelFromRecordUsing(fn ($r) => $r ? trim(($r->ins_fname ?? '') . ' ' . ($r->ins_lname ?? '')) : '—')
                        ->searchable(),
                    Select::make('course_id')
                        ->label('Course')
                        ->relationship('course', 'course_name')
                        ->searchable(),
                    TextInput::make('tot_credit')
                        ->label('Credits')
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ]),

            Section::make('Mailing Address')
                ->columns(12)
                ->schema([
                    TextInput::make('stud_address')->label('Street')->maxLength(200)->columnSpan(12),
                    TextInput::make('stud_city')->label('City')->maxLength(100)->columnSpan(4),
                    TextInput::make('stud_state')->label('State / Province')
                        ->helperText('US: 2-letter (IL, TX, CA). Intl: free-text.')
                        ->maxLength(100)->columnSpan(3),
                    TextInput::make('stud_zip')->label('ZIP / Postal')->maxLength(20)->columnSpan(2),
                    Select::make('stud_country')->label('Country')->options(self::COUNTRIES)->searchable()->columnSpan(3),
                ]),

            Section::make('Login Credentials')
                ->description('Required to create the participant\'s login account.')
                ->visibleOn('create')
                ->columns(2)
                ->schema([
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->maxLength(100)
                        ->unique('ebc_user_master', 'user_login_id'),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required()
                        ->minLength(8),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stud_lname')->label('Last Name')->searchable()->sortable(),
                TextColumn::make('stud_fname')->label('First Name')->searchable(),
                TextColumn::make('stud_email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('stud_phone')->label('Phone')->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('instructor.ins_lname')
                    ->label('Instructor')
                    ->formatStateUsing(fn ($state, $record) => $record->instructor
                        ? trim(($record->instructor->ins_fname ?? '') . ' ' . ($record->instructor->ins_lname ?? ''))
                        : '—')
                    ->toggleable(),
                TextColumn::make('course.course_name')->label('Course')->toggleable(),
                TextColumn::make('tot_credit')->label('Credits')->sortable(),
                TextColumn::make('testResults_count')
                    ->label('Assessments')
                    ->counts('testResults'),
                TextColumn::make('stud_country')->label('Country')->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('created_at')->label('Registered')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('inst_id')
                    ->label('Instructor')
                    ->relationship('instructor', 'ins_lname')
                    ->getOptionLabelFromRecordUsing(fn ($r) => $r ? trim(($r->ins_fname ?? '') . ' ' . ($r->ins_lname ?? '')) : '—')
                    ->searchable(),
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'course_name')
                    ->searchable(),
                Filter::make('has_credit')
                    ->label('Has credits')
                    ->query(fn (Builder $q) => $q->where('tot_credit', '>', 0)),
            ])
            ->actions([
                Action::make('addCredit')
                    ->label('+ Credit')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->action(fn (Participant $r) => $r->increment('tot_credit'))
                    ->requiresConfirmation(),

                Action::make('removeCredit')
                    ->label('− Credit')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->visible(fn (Participant $r) => $r->tot_credit > 0)
                    ->action(fn (Participant $r) => $r->decrement('tot_credit'))
                    ->requiresConfirmation(),

                EditAction::make(),
            ])
            ->defaultSort('stud_lname');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit'   => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
