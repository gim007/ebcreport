<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('configureSections')
                ->label('Report sections')
                ->icon('heroicon-o-squares-2x2')
                ->url(fn () => static::$resource::getUrl('sections', ['record' => $this->record])),
            DeleteAction::make(),
        ];
    }
}
