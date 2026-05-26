<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            // [code, name, sort_order, is_active]
            ['S-01', 'Cover Page',                         1,  true],
            ['S-02', 'Introduction',                       2,  true],
            ['S-03', 'How to Use This Report',             3,  true],
            ['S-04', 'Overview of the DISC Model',         4,  true],
            ['S-05', 'Scoring Summary & Basic Profiles',   5,  true],
            ['S-06', 'Profile Interpretation — Normal',    6,  true],
            ['S-07', 'Profile Interpretation — Stress',    7,  true],
            ['S-08', 'Overview',                           8,  true],
            ['S-09', 'Motivating Factors',                 9,  true],
            ['S-10', 'Strengths & Advantages',            10,  true],
            ['S-11', 'Blind Spots & Limitations',         11,  true],
            ['S-12', 'Communication Preferences',         12,  true],
            ['S-13', 'Decision-Making Style',             13,  true],
            ['S-14', 'Behavior Under Pressure & Stress',  14,  true],
            ['S-15', 'Conflict Style',                    15,  true],
            ['S-16', 'How Others Perceive You',           16,  true],
            ['S-17', 'Profile Tensions',                  17,  true],
            ['S-18', 'Working With the D Style',          18,  true],
            ['S-19', 'Working With the I Style',          19,  true],
            ['S-20', 'Working With the S Style',          20,  true],
            ['S-21', 'Working With the C Style',          21,  true],
            ['S-22', 'Where to Go From Here',             22,  true],
            ['S-23', 'Glossary & FAQ',                    23,  true],
            // Future content — client provides copy before enabling
            ['S-24', 'Sales Style',                       24,  false],
            ['S-25', 'Team Collaboration',                25,  false],
            ['S-26', 'Leadership Style',                  26,  false],
            ['S-27', 'Emotional Intelligence Indicators', 27,  false],
            // R-17: empty infrastructure slots
            ['S-28', 'Reserved Slot 1',                   28,  false],
            ['S-29', 'Reserved Slot 2',                   29,  false],
            ['S-30', 'Reserved Slot 3',                   30,  false],
            ['S-31', 'Reserved Slot 4',                   31,  false],
            ['S-32', 'Reserved Slot 5',                   32,  false],
        ];

        foreach ($sections as [$code, $name, $sort, $active]) {
            DB::table('ebc_report_sections')->updateOrInsert(
                ['code' => $code],
                [
                    'name'        => $name,
                    'sort_order'  => $sort,
                    'is_active'   => $active,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
