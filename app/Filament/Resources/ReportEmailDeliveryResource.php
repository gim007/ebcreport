<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\ReportEmailDeliveryResource\Pages;
use App\Jobs\GenerateAndEmailReportJob;
use App\Models\ReportEmailDelivery;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportEmailDeliveryResource extends Resource
{
    protected static ?string $model = ReportEmailDelivery::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Report Deliveries';
    protected static ?int    $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('testResult.participant.full_name')
                    ->label('Participant')
                    ->searchable(['stud_fname', 'stud_lname'])
                    ->placeholder('—'),

                TextColumn::make('recipient_email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sent'    => 'success',
                        'failed'  => 'danger',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->placeholder('—'),

                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(50)
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent'    => 'Sent',
                        'failed'  => 'Failed',
                    ]),
            ])
            ->actions([
                Action::make('resend')
                    ->label('Resend')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (ReportEmailDelivery $r) => $r->hasFailed() || $r->wasSent())
                    ->action(function (ReportEmailDelivery $r) {
                        $result = $r->testResult;
                        if (! $result) {
                            Notification::make()->danger()->title('No test result linked.')->send();
                            return;
                        }
                        $r->update(['status' => 'pending', 'error_message' => null, 'sent_at' => null]);
                        GenerateAndEmailReportJob::dispatch($result);
                        Notification::make()->success()->title('Re-queued for delivery.')->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportEmailDeliveries::route('/'),
        ];
    }
}
