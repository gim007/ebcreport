<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\InstructorResource\Pages;
use App\Models\Instructor;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstructorResource extends Resource
{
    protected static ?string $model = Instructor::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int    $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ins_fname')->label('First Name')->required()->maxLength(100),
            TextInput::make('ins_lname')->label('Last Name')->required()->maxLength(100),
            TextInput::make('ins_email')->label('Email')->email()->maxLength(255),

            Select::make('uni_id')
                ->label('Organization')
                ->relationship('organization', 'uni_name')
                ->searchable()
                ->required(),

            Select::make('admin_approval')
                ->label('Approval Status')
                ->options(['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'])
                ->default('Approved')
                ->required(),

            Toggle::make('is_hidden')
                ->label('Hidden from participants'),

            Section::make('Login Credentials')
                ->description('Required to create the instructor\'s login account.')
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
                TextColumn::make('ins_lname')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ins_fname')
                    ->label('First Name')
                    ->searchable(),

                TextColumn::make('ins_email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('organization.uni_name')
                    ->label('Organization')
                    ->sortable(),

                TextColumn::make('admin_approval')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default    => 'warning',
                    }),

                IconColumn::make('is_hidden')
                    ->label('Hidden')
                    ->boolean()
                    ->trueColor('warning'),
            ])
            ->filters([
                SelectFilter::make('admin_approval')
                    ->label('Status')
                    ->options(['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected']),

                TernaryFilter::make('is_hidden')
                    ->label('Hidden'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Instructor $r) => $r->admin_approval !== 'Approved')
                    ->action(fn (Instructor $r) => $r->update(['admin_approval' => 'Approved']))
                    ->requiresConfirmation(),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Instructor $r) => $r->admin_approval !== 'Rejected')
                    ->action(fn (Instructor $r) => $r->update(['admin_approval' => 'Rejected']))
                    ->requiresConfirmation(),

                Action::make('toggleHidden')
                    ->label(fn (Instructor $r) => $r->is_hidden ? 'Show' : 'Hide')
                    ->icon('heroicon-o-eye-slash')
                    ->action(fn (Instructor $r) => $r->update(['is_hidden' => ! $r->is_hidden])),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('ins_lname');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInstructors::route('/'),
            'create' => Pages\CreateInstructor::route('/create'),
            'edit'   => Pages\EditInstructor::route('/{record}/edit'),
        ];
    }
}
