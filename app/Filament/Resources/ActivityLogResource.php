<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

/**
 * Read-only audit viewer over the Spatie activity_log table.
 *
 * Goes beyond R-08 (which only mandates ERROR logging). Captures who
 * mutated which subject and what changed — useful for support cases,
 * GDPR data-subject requests, and accidental-edit investigations.
 *
 * No create / edit / delete: this is a forensic log, not a workspace.
 */
class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Activity Log';
    protected static ?int    $navigationSort  = 99;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);  // never used; resource is read-only
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($r): bool { return false; }
    public static function canDelete($r): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('M j, Y g:i a')
                    ->sortable(),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state, Activity $r) => $state
                        ? class_basename($state) . ($r->subject_id ? " #{$r->subject_id}" : '')
                        : '—')
                    ->searchable(),

                TextColumn::make('causer_type')
                    ->label('By')
                    ->formatStateUsing(function (Activity $r) {
                        if ($r->causer_type === null) {
                            return 'System / Unknown';
                        }
                        $name = class_basename($r->causer_type);
                        if ($r->causer && method_exists($r->causer, 'getAttribute')) {
                            $display = $r->causer->admin_name
                                ?? $r->causer->user_login_id
                                ?? $r->causer->user_email
                                ?? "{$name} #{$r->causer_id}";
                            return "{$display} ({$name})";
                        }
                        return "{$name} #{$r->causer_id}";
                    }),

                TextColumn::make('description')
                    ->label('Description')
                    ->toggleable(),

                TextColumn::make('changes_summary')
                    ->label('Changes')
                    ->getStateUsing(function (Activity $r) {
                        // Spatie v5 puts the dirty diff in the dedicated
                        // `attribute_changes` column. `properties` is for
                        // arbitrary metadata (usually empty here).
                        $diff  = $r->attribute_changes;
                        $attrs = is_object($diff) ? ($diff->get('attributes', []) ?? []) : ($diff['attributes'] ?? []);
                        $old   = is_object($diff) ? ($diff->get('old', [])        ?? []) : ($diff['old']        ?? []);
                        if (! $attrs && ! $old) {
                            return '—';
                        }
                        $keys = array_unique(array_merge(array_keys($attrs), array_keys($old)));
                        sort($keys);
                        $bits = [];
                        foreach (array_slice($keys, 0, 3) as $k) {
                            $from = $old[$k]   ?? '∅';
                            $to   = $attrs[$k] ?? '∅';
                            $bits[] = "{$k}: " . self::truncate((string) $from) . ' → ' . self::truncate((string) $to);
                        }
                        if (count($keys) > 3) {
                            $bits[] = '+ ' . (count($keys) - 3) . ' more';
                        }
                        return implode('  ·  ', $bits);
                    })
                    ->wrap()
                    ->tooltip(fn (Activity $r) => json_encode($r->attribute_changes, JSON_PRETTY_PRINT)),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('subject_type')
                    ->label('Subject type')
                    ->options(function () {
                        return Activity::query()
                            ->whereNotNull('subject_type')
                            ->distinct()
                            ->pluck('subject_type', 'subject_type')
                            ->map(fn ($v) => class_basename($v))
                            ->all();
                    }),
                Filter::make('today')
                    ->label('Today only')
                    ->query(fn (Builder $q) => $q->whereDate('created_at', today())),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLog::route('/'),
            'view'  => Pages\ViewActivityLog::route('/{record}'),
        ];
    }

    private static function truncate(string $s, int $len = 32): string
    {
        return mb_strlen($s) > $len ? mb_substr($s, 0, $len - 1) . '…' : $s;
    }
}
