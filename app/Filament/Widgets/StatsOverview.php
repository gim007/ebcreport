<?php

namespace App\Filament\Widgets;

use App\Models\Course;
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

            Stat::make('Instructors', Instructor::count())
                ->description(sprintf(
                    '%d active · %d pending',
                    Instructor::where('admin_approval', 'Approved')->where('is_hidden', false)->count(),
                    Instructor::where('admin_approval', 'Pending')->count(),
                ))
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-o-academic-cap')
                ->color('warning'),

            Stat::make('Courses', Course::count())
                ->description(sprintf('%d active', Course::where('is_hidden', false)->count()))
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-o-book-open')
                ->color('primary'),

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
