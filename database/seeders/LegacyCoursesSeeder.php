<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the 373 real legacy courses with their original IDs, names, prices,
 * terms, and instructor mappings. No PII — these are course metadata only.
 * Courses with a missing inst_id (orphaned in the dump) are skipped.
 *
 * Depends on LegacyInstructorsSeeder running first so the inst_id FK exists.
 */
class LegacyCoursesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/courses.jsonl');
        if (! is_file($path)) {
            $this->command?->warn("Missing fixture file: {$path}");
            return;
        }

        $instructorIds = DB::table('ebc_instructor')->pluck('ins_id')->all();
        $instructorIds = array_flip($instructorIds);

        $count = $skipped = 0;
        foreach ($this->readJsonLines($path) as $row) {
            if (! isset($instructorIds[$row['inst_id']])) {
                $skipped++;
                continue;
            }

            DB::table('ebc_course')->updateOrInsert(
                ['course_id' => $row['course_id']],
                [
                    'inst_id'       => $row['inst_id'],
                    'course_name'   => $row['course_name']   ?? "Course #{$row['course_id']}",
                    'course_code'   => $row['course_code']   ?? null,
                    'term'          => $row['term']          ?? '',
                    'schedule_time' => $row['schedule_time'] ?? '',
                    'course_price'  => $row['course_price']  ?? 0,
                    'is_hidden'     => false,
                    // expiry_date left null = never expires (legacy used today+300
                    // but most legacy courses are already historical; null is more
                    // honest for a seed and lets them remain selectable in dev)
                ]
            );
            $count++;
        }

        $this->command?->info("Seeded {$count} courses ({$skipped} skipped: orphan inst_id)");
    }

    private function readJsonLines(string $path): iterable
    {
        $fh = fopen($path, 'r');
        try {
            while (($line = fgets($fh)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                yield json_decode($line, true, flags: JSON_THROW_ON_ERROR);
            }
        } finally {
            fclose($fh);
        }
    }
}
