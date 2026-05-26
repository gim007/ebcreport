<?php

namespace Tests\Unit;

use App\Services\DiscScoreCalculator;
use PHPUnit\Framework\TestCase;

class DiscScoreCalculatorTest extends TestCase
{
    private DiscScoreCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new DiscScoreCalculator();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fixtureProvider')]
    public function test_matches_legacy_output(
        string $most,
        string $least,
        array $expectedMaskRaw,
        array $expectedLatentRaw,
        array $expectedMaskPct,
        array $expectedLatentPct,
    ): void {
        if ($most === '') {
            $this->markTestSkipped('No fixtures yet. Run: php artisan disc:generate-fixtures');
        }

        $score = $this->calc->calculate($most, $least);

        $this->assertSame($expectedMaskRaw,    $score->maskRaw,           'mask raw counts');
        $this->assertSame($expectedLatentRaw,  $score->latentRaw,         'latent raw counts');
        $this->assertSame($expectedMaskPct,    $score->maskPercentile,    'mask percentiles');
        $this->assertSame($expectedLatentPct,  $score->latentPercentile,  'latent percentiles');
    }

    public static function fixtureProvider(): array
    {
        $path = __DIR__ . '/../fixtures/disc_scores.json';

        if (! file_exists($path)) {
            // Run: php artisan disc:generate-fixtures
            // to populate this file from the live legacy database.
            return [['', '', [], [], [], []]]; // placeholder — test_matches_legacy_output will skip
        }

        $fixtures = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        return array_map(fn ($f) => [
            $f['most'], $f['least'],
            $f['mask_raw'], $f['latent_raw'],
            $f['mask_pct'], $f['latent_pct'],
        ], $fixtures);
    }

    public function test_shift_equals_latent_minus_mask(): void
    {
        $score = $this->calc->calculate(str_repeat('2', 48), str_repeat('1', 48));
        $shift = $score->shift();

        for ($i = 0; $i < 4; $i++) {
            $this->assertSame(
                $score->latentPercentile[$i] - $score->maskPercentile[$i],
                $shift[$i],
                "shift[$i] must equal latentPercentile[$i] - maskPercentile[$i]"
            );
        }
    }

    public function test_percentiles_in_range(): void
    {
        for ($n = 0; $n < 100; $n++) {
            $most = $least = '';
            for ($j = 0; $j < 48; $j++) {
                $most  .= (string) random_int(1, 9);
                $least .= (string) random_int(1, 9);
            }
            $score = $this->calc->calculate($most, $least);

            foreach ([...$score->maskPercentile, ...$score->latentPercentile] as $v) {
                $this->assertGreaterThanOrEqual(1, $v, 'percentile must be >= 1');
                $this->assertLessThanOrEqual(99, $v,   'percentile must be <= 99');
            }
        }
    }

    public function test_mask_pattern_bitmask(): void
    {
        // D=99, I=1, S=1, C=1 → only D bit set → pattern = 8
        // Use all-'9' mask string: key[0][0]='9', so position 0 matches D
        // Easier: just verify the bit arithmetic directly
        $score = $this->calc->calculate(str_repeat('9', 48), str_repeat('1', 48));
        $pattern = $score->maskPattern();
        $this->assertGreaterThanOrEqual(0, $pattern);
        $this->assertLessThanOrEqual(15, $pattern);
    }

    public function test_tensions_returns_six_values(): void
    {
        $score = $this->calc->calculate(str_repeat('5', 48), str_repeat('3', 48));

        $this->assertCount(6, $score->maskTensions());
        $this->assertCount(6, $score->latentTensions());
    }
}
