<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// R-15: per-organization section override (enabled/disabled per section per org)
class OrgSectionConfig extends Model
{
    protected $table = 'ebc_org_section_config';

    protected $fillable = [
        'org_id',
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(ReportSection::class, 'section_code', 'code');
    }
}
