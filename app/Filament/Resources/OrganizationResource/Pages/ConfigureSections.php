<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use App\Models\Organization;
use App\Services\ReportSectionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;

/**
 * R-15 / R-16 / R-17 — per-organization report-section configuration.
 *
 * Loads ALL active sections (R-17 hides reserved/inactive slots), hydrates
 * each toggle from the per-org override (default: enabled), and persists
 * them atomically via ReportSectionService. Persistence is keyed to a
 * single org_id, which is what fixes R-16 — switching between orgs
 * loads a fresh, correct toggle state instead of carrying state over.
 */
class ConfigureSections extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string $resource = OrganizationResource::class;

    protected string $view = 'filament.resources.organization.configure-sections';

    public ?array $data = [];

    public Organization $record;

    public static function getSlug(?Panel $panel = null): string
    {
        return '{record}/sections';
    }

    public function mount(int | string $record): void
    {
        $this->record = Organization::query()->findOrFail($record);

        $overrides = app(ReportSectionService::class)
            ->sectionsWithOverridesFor((int) $this->record->getKey());

        $this->data = $overrides
            ->mapWithKeys(fn ($row) => [$row['code'] => $row['enabled']])
            ->all();

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        $sections = app(ReportSectionService::class)
            ->sectionsWithOverridesFor((int) ($this->record?->getKey() ?? 0));

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Report Sections')
                    ->description('Toggle each section on or off for this organization. Changes apply to every report generated for participants in this organization.')
                    ->schema(
                        $sections->map(fn ($row) => Toggle::make($row['code'])
                            ->label("{$row['code']} — {$row['name']}")
                            ->inline(false)
                            ->default(true)
                        )->all()
                    ),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        app(ReportSectionService::class)
            ->saveOverridesFor((int) $this->record->getKey(), $state);

        Notification::make()
            ->title('Section configuration saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action('save'),
        ];
    }

    public function getTitle(): string
    {
        return "Sections — {$this->record->uni_name}";
    }
}
