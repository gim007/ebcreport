<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the 46 legacy instructors (relations + approval status preserved).
 *
 * R-staging requirement: "An anonymized copy of the database will be
 * provided for development — real participant data will not be shared."
 * Structural data (ins_id ↔ uni_id, admin_approval) is preserved verbatim
 * so courses/test_results keep their relations; all PII-adjacent fields
 * (title, gender, names, email, phone, address, timezone) are replaced
 * with deterministic Faker output.
 *
 * Faker seeded with a fixed key for stable re-runs (screenshot diffs).
 *
 * email_verified_at is set on the User row to NOW() — legacy instructors
 * had already gone through verification by the time the dump was taken,
 * so we mirror that state in the dev DB.
 */
class LegacyInstructorsSeeder extends Seeder
{
    private const TITLES = ['', '', '', 'Mr.', 'Ms.', 'Dr.', 'Prof.'];  // weighted: most blank

    private const TIMEZONES = [
        'America/New_York',
        'America/Chicago',
        'America/Denver',
        'America/Los_Angeles',
        'America/Anchorage',
        'Pacific/Honolulu',
        'Europe/London',
        'Europe/Berlin',
        'Asia/Kolkata',
        'Asia/Singapore',
        'Australia/Sydney',
    ];

    private const US_STATES = [
        'IL','TX','CA','NY','FL','PA','OH','MA','GA','WA',
        'MI','MN','CO','OR','AZ','VA','NJ','NC','WI','MD',
    ];

    public function run(): void
    {
        $path = database_path('seeders/data/instructors.jsonl');
        if (! is_file($path)) {
            $this->command?->warn("Missing fixture file: {$path}");
            return;
        }

        $faker = FakerFactory::create();
        $faker->seed(1234);  // reproducible

        $defaultPassword = Hash::make('password');

        $count = 0;
        foreach ($this->readJsonLines($path) as $row) {
            $first  = $faker->firstName();
            $last   = $faker->lastName();
            $title  = $faker->randomElement(self::TITLES);
            $gender = $faker->randomElement(['Male', 'Female', 'Female', 'Male', 'Other']);
            $hasApt = $faker->boolean(30);

            // Create or update User row
            DB::table('ebc_user_master')->updateOrInsert(
                ['user_login_id' => "instructor{$row['ins_id']}"],
                [
                    'user_email'        => "instructor{$row['ins_id']}@example.test",
                    'email_verified_at' => now(),    // legacy instructors were already verified
                    'user_password'     => $defaultPassword,
                    'user_type'         => 'ins',
                    'user_status'       => 'Active',
                ]
            );

            $user = DB::table('ebc_user_master')
                ->where('user_login_id', "instructor{$row['ins_id']}")
                ->first();

            DB::table('ebc_instructor')->updateOrInsert(
                ['ins_id' => $row['ins_id']],
                [
                    'user_id'          => $user->user_id,
                    'uni_id'           => $row['uni_id'],

                    // Identity (Faker)
                    'ins_title'        => $title !== '' ? $title : null,
                    'ins_fname'        => $first,
                    'ins_lname'        => $last,
                    'ins_gender'       => $gender,
                    'ins_email'        => "instructor{$row['ins_id']}@example.test",

                    // Contact (Faker; phone is R-31)
                    'ins_phone'        => $faker->phoneNumber(),

                    // Mailing address (Faker — US-format)
                    'ins_address'      => $faker->streetAddress(),
                    'ins_address_cont' => $hasApt ? ('Suite ' . $faker->numberBetween(100, 999)) : null,
                    'ins_city'         => $faker->city(),
                    'ins_state'        => $faker->randomElement(self::US_STATES),
                    'ins_zip'          => $faker->postcode(),
                    'ins_country'      => 'US',

                    // Timezone (random from common IANA names)
                    'ins_timezone'     => $faker->randomElement(self::TIMEZONES),

                    'is_hidden'        => false,
                    'admin_approval'   => $row['admin_approval'],
                ]
            );
            $count++;
        }

        $this->command?->info("Seeded {$count} instructors (PII anonymized; default password: 'password'; emails marked verified)");
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
