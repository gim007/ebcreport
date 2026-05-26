<?php

namespace App\Filament\Widgets;

use App\Models\Instructor;
use App\Models\Organization;
use App\Models\Participant;
use App\Models\ReportEmailDelivery;
use App\Models\TestResult;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Organizations', Organization::count())
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Instructors (Pending)', Instructor::where('admin_approval', 'Pending')->count())
                ->icon('heroicon-o-academic-cap')
                ->color('warning'),

            Stat::make('Participants', Participant::count())
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Assessments Taken', TestResult::whereNotNull('most_result_str')->count())
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Failed Deliveries', ReportEmailDelivery::where('status', 'failed')->count())
                ->icon('heroicon-o-envelope-open')
                ->color('danger'),
        ];
    }
}
