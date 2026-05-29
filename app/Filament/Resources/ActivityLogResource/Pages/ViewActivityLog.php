<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Spatie\Activitylog\Models\Activity;

/**
 * Per-entry forensic detail. Shows the full diff (old vs new) plus the
 * subject + causer + description + raw properties JSON. Read-only.
 */
class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('created_at')->label('When')->dateTime('M j, Y g:i a'),
            TextEntry::make('event')->badge(),
            TextEntry::make('description'),
            TextEntry::make('subject_summary')
                ->label('Subject')
                ->getStateUsing(fn (Activity $r) => $r->subject_type
                    ? class_basename($r->subject_type) . ($r->subject_id ? " #{$r->subject_id}" : '')
                    : '—'),
            TextEntry::make('causer_summary')
                ->label('Causer')
                ->getStateUsing(function (Activity $r) {
                    if ($r->causer_type === null) {
                        return 'System / Unknown';
                    }
                    $name = class_basename($r->causer_type);
                    if ($r->causer) {
                        $display = $r->causer->admin_name
                            ?? $r->causer->user_login_id
                            ?? $r->causer->user_email
                            ?? "{$name} #{$r->causer_id}";
                        return "{$display} ({$name} #{$r->causer_id})";
                    }
                    return "{$name} #{$r->causer_id}";
                }),
            KeyValueEntry::make('changes.old')
                ->label('Before')
                ->getStateUsing(function (Activity $r) {
                    $diff = $r->attribute_changes;
                    $old  = is_object($diff) ? ($diff->get('old', []) ?? []) : ($diff['old'] ?? []);
                    return $old ?: ['—' => '(nothing recorded)'];
                }),
            KeyValueEntry::make('changes.attributes')
                ->label('After')
                ->getStateUsing(function (Activity $r) {
                    $diff = $r->attribute_changes;
                    $new  = is_object($diff) ? ($diff->get('attributes', []) ?? []) : ($diff['attributes'] ?? []);
                    return $new ?: ['—' => '(nothing recorded)'];
                }),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
