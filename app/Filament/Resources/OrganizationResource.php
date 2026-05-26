<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Organizations';
    protected static ?int    $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('uni_name')
                ->label('Organization Name')
                ->required()
                ->maxLength(255),

            TextInput::make('course_price')
                ->label('Default Course Price ($)')
                ->numeric()
                ->nullable(),

            Toggle::make('is_hidden')
                ->label('Hidden from participants')
                ->default(false),

            SpatieMediaLibraryFileUpload::make('logo')
                ->collection('logo')
                ->label('Logo')
                ->image()
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('4:1')
                ->imageResizeTargetWidth('800')
                ->imageResizeTargetHeight('200')
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->label('')
                    ->size(40)
                    ->circular(false),

                TextColumn::make('uni_name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('instructors_count')
                    ->label('Instructors')
                    ->counts('instructors')
                    ->sortable(),

                IconColumn::make('is_hidden')
                    ->label('Hidden')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_hidden')
                    ->label('Hidden')
                    ->placeholder('All')
                    ->trueLabel('Hidden only')
                    ->falseLabel('Visible only'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('uni_name');
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'    => Pages\ListOrganizations::route('/'),
            'create'   => Pages\CreateOrganization::route('/create'),
            'edit'     => Pages\EditOrganization::route('/{record}/edit'),
            'sections' => Pages\ConfigureSections::route('/{record}/sections'),
        ];
    }
}
