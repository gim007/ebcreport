<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('configureSections')
                ->label('Section overrides')
                ->icon('heroicon-o-squares-2x2')
                ->url(fn () => static::$resource::getUrl('sections', ['record' => $this->record])),
            DeleteAction::make(),
        ];
    }
}
