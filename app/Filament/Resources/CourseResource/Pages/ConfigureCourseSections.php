<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Course;
use App\Services\ReportSectionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

/**
 * R-15 per-course section configuration page.
 *
 * Course-level overrides take priority over the organization-level defaults
 * configured under OrganizationResource::ConfigureSections. The resolution
 * order is implemented in ReportSectionService::enabledSectionsFor().
 *
 * Uses Filament's InteractsWithRecord trait so the {record} slug binds to
 * the Course model via the same flow as the standard EditRecord page.
 */
class ConfigureCourseSections extends Page implements HasSchemas
{
    use InteractsWithRecord;
    use InteractsWithSchemas;

    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.resources.course.configure-sections';

    public ?array $data = [];

    public static function getSlug(?Panel $panel = null): string
    {
        return '{record}/sections';
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $orgId = $this->resolveOrgId();
        if ($orgId === null) {
            $this->data = [];
            $this->form->fill($this->data);
            return;
        }

        $overrides = app(ReportSectionService::class)
            ->sectionsWithOverridesFor($orgId, (int) $this->record->getKey());

        $this->data = $overrides
            ->mapWithKeys(fn ($row) => [$row['code'] => $row['enabled']])
            ->all();

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        $orgId    = $this->resolveOrgId();
        $courseId = $this->record instanceof Course ? (int) $this->record->getKey() : 0;
        $sections = $orgId !== null
            ? app(ReportSectionService::class)->sectionsWithOverridesFor($orgId, $courseId)
            : collect();

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Section Overrides for This Course')
                    ->description('Toggle each section on or off for this specific course. Course-level settings take priority over the organization-level defaults. Leave a setting as-is to inherit from the organization default.')
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
        $orgId = $this->resolveOrgId();
        if ($orgId === null) {
            Notification::make()
                ->title('Cannot save — this course has no associated organization')
                ->danger()
                ->send();
            return;
        }

        app(ReportSectionService::class)
            ->saveOverridesFor($orgId, $this->form->getState(), (int) $this->record->getKey());

        Notification::make()
            ->title('Course section overrides saved')
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

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return $this->record instanceof Course
            ? "Sections — {$this->record->course_name}"
            : 'Course section configuration';
    }

    private function resolveOrgId(): ?int
    {
        if (! $this->record instanceof Course) {
            return null;
        }
        $orgId = $this->record->instructor?->organization?->getKey();
        return $orgId !== null ? (int) $orgId : null;
    }
}
