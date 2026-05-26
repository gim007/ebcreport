<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Generates byte-to-byte test fixtures by running the ORIGINAL legacy scoring
 * logic directly against the live database.
 *
 * Usage:
 *   php artisan disc:generate-fixtures --limit=50 --output=tests/fixtures/disc_scores.json
 *
 * Connect this command to the LEGACY database (ebcdiscbeta2) to capture ground truth.
 * Run once; commit the JSON; never delete it.
 */
class GenerateDiscFixtures extends Command
{
    protected $signature = 'disc:generate-fixtures
                            {--limit=50 : Number of real test results to export}
                            {--output=tests/fixtures/disc_scores.json : Output file path}';

    protected $description = 'Export DISC score fixtures from the legacy DB for byte-identical testing';

    private const KEYS = [
        "923291199232331333113991191229121229129393292313",
        "139913312919119229929133239132299112233229133231",
        "391132923323292912292219313993313391912111321929",
        "212329231191923191331322922311932933391932919192",
    ];

    private const NORMM = [
        [1,4,7,9,12,16,22,25,38,45,52,58,62,65,68,72,76,83,85,87,89,91,93,94,95,96,97,97,98,98,99,99,99,99,99,99],
        [1,3,6,10,17,26,35,42,48,57,64,71,76,81,84,88,90,93,95,96,89,99,93,94,95,96,97,97,98,98,99,99,99,99,99,99],
        [1,1,2,3,6,11,14,21,24,29,35,45,50,57,64,70,77,82,87,91,94,96,98,99,99,99,99,99,99,99,99,99,99,99,99,99],
        [1,1,2,2,3,5,7,13,16,19,22,29,38,44,54,58,65,73,79,85,88,92,97,98,99,99,99,99,99,99,99,99,99,99,99,99],
    ];

    private const NORML = [
        [99,98,93,90,88,85,82,79,74,69,63,60,56,53,46,38,30,26,21,17,13,11,7,4,4,3,2,1,1,1,1,1,1,1,1,1],
        [99,99,99,99,94,93,91,87,81,74,69,65,57,51,46,39,33,29,23,19,17,13,9,6,3,3,2,1,1,1,1,1,1,1,1,1],
        [99,99,99,97,93,88,84,76,66,54,49,45,37,30,24,19,14,9,5,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
        [99,98,94,91,84,77,66,59,53,45,37,33,27,19,15,9,6,4,3,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
    ];

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $output = base_path($this->option('output'));

        $rows = DB::table('ebc_test_result')
            ->whereNotNull('most_result_str')
            ->whereNotNull('least_result_str')
            ->where('most_result_str', '!=', '')
            ->where('least_result_str', '!=', '')
            ->limit($limit)
            ->get(['id', 'most_result_str', 'least_result_str']);

        if ($rows->isEmpty()) {
            $this->error('No rows found in ebc_test_result. Check DB connection.');
            return self::FAILURE;
        }

        $fixtures = $rows->map(function ($row) {
            $most   = substr($row->most_result_str,   0, 48);
            $least  = substr($row->least_result_str,  0, 48);

            [$m, $ls, $mp, $lp] = $this->score($most, $least);

            return [
                'result_id'  => $row->id,
                'most'       => $most,
                'least'      => $least,
                'mask_raw'   => $m,
                'latent_raw' => $ls,
                'mask_pct'   => $mp,
                'latent_pct' => $lp,
            ];
        })->values()->toArray();

        $dir = dirname($output);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($output, json_encode($fixtures, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        $this->info("Wrote {$rows->count()} fixtures to {$output}");
        return self::SUCCESS;
    }

    private function score(string $maskStr, string $latentStr): array
    {
        $m  = [0, 0, 0, 0];
        $ls = [0, 0, 0, 0];

        for ($i = 0; $i <= 47; $i++) {
            for ($j = 0; $j <= 3; $j++) {
                $k = self::KEYS[$j][$i];
                if (isset($maskStr[$i])   && $maskStr[$i]   === $k) $m[$j]++;
                if (isset($latentStr[$i]) && $latentStr[$i] === $k) $ls[$j]++;
            }
        }

        $mp = $lp = [0, 0, 0, 0];
        for ($j = 0; $j <= 3; $j++) {
            $mp[$j] = self::NORMM[$j][max(1, $m[$j])  - 1];
            $lp[$j] = self::NORML[$j][max(1, $ls[$j]) - 1];
        }

        return [$m, $ls, $mp, $lp];
    }
}
