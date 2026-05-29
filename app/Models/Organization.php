<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// UI label: "Organization" (R-36). DB table: ebc_university (unchanged).
class Organization extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $table      = 'ebc_university';
    protected $primaryKey = 'uni_id';

    protected $fillable = [
        'uni_name',
        'logo_path',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function instructors(): HasMany
    {
        return $this->hasMany(Instructor::class, 'uni_id', 'uni_id');
    }

    public function sectionConfigs(): HasMany
    {
        return $this->hasMany(OrgSectionConfig::class, 'org_id', 'uni_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile(); // R-18/R-19
    }
}
