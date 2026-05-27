<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a 100-row anonymized sample of legacy participants. Each row
 * preserves the original (stud_id, course_id, inst_id) tuple so that
 * existing test_result rows / report scenarios keep their relations,
 * but all PII (names, emails, phones, addresses) is replaced with
 * deterministic Faker output.
 *
 * R-staging requirement: "real participant data will not be shared."
 *
 * Default password is `password` for every seeded participant — only
 * meaningful in dev. Production seeds nothing here.
 */
class LegacyParticipantsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/students.jsonl');
        if (! is_file($path)) {
            $this->command?->warn("Missing fixture file: {$path}");
            return;
        }

        $faker = FakerFactory::create();
        $faker->seed(5678);

        $defaultPassword = Hash::make('password');

        $courseIds = array_flip(DB::table('ebc_course')->pluck('course_id')->all());
        $instIds   = array_flip(DB::table('ebc_instructor')->pluck('ins_id')->all());

        $usStates    = ['IL','TX','CA','NY','FL','PA','OH','MA','GA','WA','MI','MN','CO','OR','AZ','VA','NJ','NC','WI'];

        $count = $skipped = 0;
        foreach ($this->readJsonLines($path) as $row) {
            // Skip if the related course or instructor isn't present
            if (! isset($courseIds[$row['course_id']]) || ! isset($instIds[$row['inst_id']])) {
                $skipped++;
                continue;
            }

            $first = $faker->firstName();
            $last  = $faker->lastName();

            DB::table('ebc_user_master')->updateOrInsert(
                ['user_login_id' => "participant{$row['stud_id']}"],
                [
                    'user_email'        => "participant{$row['stud_id']}@example.test",
                    'email_verified_at' => now(),    // legacy participants were already active
                    'user_password'     => $defaultPassword,
                    'user_type'         => 'stud',
                    'user_status'       => 'Active',
                ]
            );

            $user = DB::table('ebc_user_master')->where('user_login_id', "participant{$row['stud_id']}")->first();

            DB::table('ebc_student')->updateOrInsert(
                ['stud_id' => $row['stud_id']],
                [
                    'user_id'      => $user->user_id,
                    'stud_fname'   => $first,
                    'stud_lname'   => $last,
                    'stud_email'   => "participant{$row['stud_id']}@example.test",
                    'stud_gender'  => in_array($row['gender'], ['Male','Female'], true) ? $row['gender'] : 'Prefer not to say',
                    'stud_phone'   => $faker->phoneNumber(),
                    'stud_address' => $faker->streetAddress(),
                    'stud_city'    => $faker->city(),
                    'stud_state'   => $faker->randomElement($usStates),
                    'stud_zip'     => $faker->postcode(),
                    'stud_country' => 'US',
                    'tot_credit'   => (int) ($row['tot_credit'] ?? 0),
                    'inst_id'      => $row['inst_id'],
                    'course_id'    => $row['course_id'],
                ]
            );
            $count++;
        }

        $this->command?->info("Seeded {$count} participants (PII anonymized; {$skipped} skipped: missing course/inst)");
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
