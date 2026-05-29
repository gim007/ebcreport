<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Suppress activity_log writes during seeding. Without this, every
        // model save produces an audit row — ~600+ noise entries on a fresh
        // seed. Audit logging resumes immediately after this command exits
        // (config is request-scoped).
        config(['activitylog.enabled' => false]);

        $this->call(ReportSectionsSeeder::class);

        // Legacy data (anonymized PII per SOW staging requirement) — order matters
        // because the *Courses* seeder skips rows whose inst_id isn't present, and
        // the *Participants* seeder skips rows whose course_id or inst_id is missing.
        $this->call([
            LegacyOrganizationsSeeder::class,
            LegacyInstructorsSeeder::class,
            LegacyCoursesSeeder::class,
            LegacyParticipantsSeeder::class,
        ]);
    }
}
