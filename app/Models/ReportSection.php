<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// R-14, R-17: global section registry. Org-level overrides live in OrgSectionConfig.
class ReportSection extends Model
{
    protected $table = 'ebc_report_sections';

    protected $fillable = [
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orgConfigs(): HasMany
    {
        return $this->hasMany(OrgSectionConfig::class, 'section_code', 'code');
    }
}
