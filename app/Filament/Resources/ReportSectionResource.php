<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\ReportSectionResource\Pages;
use App\Models\ReportSection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportSectionResource extends Resource
{
    protected static ?string $model = ReportSection::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Report Sections';
    protected static ?int    $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Section Code')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('name')
                ->label('Section Name')
                ->required()
                ->maxLength(100),

            TextInput::make('sort_order')
                ->label('Sort Order')
                ->integer()
                ->required(),

            Toggle::make('is_active')
                ->label('Active globally'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->sortable()->searchable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                Action::make('toggleActive')
                    ->label(fn (ReportSection $r) => $r->is_active ? 'Disable' : 'Enable')
                    ->icon(fn (ReportSection $r) => $r->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (ReportSection $r) => $r->is_active ? 'warning' : 'success')
                    ->action(fn (ReportSection $r) => $r->update(['is_active' => ! $r->is_active])),

                EditAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportSections::route('/'),
            'edit'  => Pages\EditReportSection::route('/{record}/edit'),
        ];
    }
}
