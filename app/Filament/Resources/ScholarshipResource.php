<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\ScholarshipResource\Pages;
use App\Models\Scholarship;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Scholarship Codes';
    protected static ?int    $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('scholarship_code')
                ->label('Code')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(100),

            Select::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                ->default('active')
                ->required(),

            TextInput::make('max_uses')
                ->label('Max Uses')
                ->integer()
                ->minValue(1)
                ->default(1)
                ->required(),

            TextInput::make('use_count')
                ->label('Used So Far')
                ->integer()
                ->default(0)
                ->disabled(),

            DatePicker::make('expires_at')
                ->label('Expires At')
                ->nullable(),

            TextInput::make('comment')
                ->label('Notes')
                ->maxLength(500)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scholarship_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'active' ? 'success' : 'danger'),

                TextColumn::make('use_count')->label('Used'),
                TextColumn::make('max_uses')->label('Max'),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->placeholder('No expiry'),

                TextColumn::make('comment')
                    ->label('Notes')
                    ->limit(40),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('scholarship_code');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListScholarships::route('/'),
            'create' => Pages\CreateScholarship::route('/create'),
            'edit'   => Pages\EditScholarship::route('/{record}/edit'),
        ];
    }
}
