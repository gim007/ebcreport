<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// R-15: per-organization / per-course section override.
// course_id NULL  → applies to the entire organization
// course_id !NULL → overrides the org-level setting for that specific course
class OrgSectionConfig extends Model
{
    protected $table = 'ebc_org_section_config';

    protected $fillable = [
        'org_id',
        'course_id',
        'section_code',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'uni_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ReportSection::class, 'section_code', 'code');
    }
}
