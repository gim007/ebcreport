<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the 20 real organizations from the legacy `ct_edbc_3d0el` production
 * dump (uni_name + course_price are not PII — they are public org metadata).
 * Original uni_id values preserved so legacy courses/instructors keep their
 * relations when the matching seeders run.
 *
 * Reset / overwrite-safe: uses updateOrInsert keyed on uni_id.
 */
class LegacyOrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/universities.jsonl');
        if (! is_file($path)) {
            $this->command?->warn("Missing fixture file: {$path}");
            return;
        }

        $count = 0;
        foreach ($this->readJsonLines($path) as $row) {
            DB::table('ebc_university')->updateOrInsert(
                ['uni_id' => $row['uni_id']],
                [
                    'uni_name'     => $row['uni_name'],
                    'course_price' => $row['course_price'] ?? null,
                    'is_hidden'    => false,
                ]
            );
            $count++;
        }

        $this->command?->info("Seeded {$count} organizations");
    }

    /**
     * @return iterable<int, array<string, mixed>>
     */
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
