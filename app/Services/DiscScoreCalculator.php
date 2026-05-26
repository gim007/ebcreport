<?php

namespace App\Services;

final class DiscScoreCalculator
{
    // Answer keys — verbatim from legacy score.php (do not modify without updating fixtures)
    private const KEYS = [
        "923291199232331333113991191229121229129393292313",  // D — Dominance
        "139913312919119229929133239132299112233229133231",  // I — Influence
        "391132923323292912292219313993313391912111321929",  // S — Steadiness
        "212329231191923191331322922311932933391932919192",  // C — Compliance
    ];

    // Mask percentile table [dimension][raw_score - 1] — ascending: higher raw → higher percentile
    private const NORMM = [
        /* D */ [1,4,7,9,12,16,22,25,38,45,52,58,62,65,68,72,76,83,85,87,89,91,93,94,95,96,97,97,98,98,99,99,99,99,99,99],
        /* I */ [1,3,6,10,17,26,35,42,48,57,64,71,76,81,84,88,90,93,95,96,89,99,93,94,95,96,97,97,98,98,99,99,99,99,99,99],
        /* S */ [1,1,2,3,6,11,14,21,24,29,35,45,50,57,64,70,77,82,87,91,94,96,98,99,99,99,99,99,99,99,99,99,99,99,99,99],
        /* C */ [1,1,2,2,3,5,7,13,16,19,22,29,38,44,54,58,65,73,79,85,88,92,97,98,99,99,99,99,99,99,99,99,99,99,99,99],
    ];

    // Latent percentile table [dimension][raw_score - 1] — descending: higher raw → lower percentile
    // Latent uses "Least-like-me" answers; a high raw Latent D score means D is being suppressed.
    private const NORML = [
        /* D */ [99,98,93,90,88,85,82,79,74,69,63,60,56,53,46,38,30,26,21,17,13,11,7,4,4,3,2,1,1,1,1,1,1,1,1,1],
        /* I */ [99,99,99,99,94,93,91,87,81,74,69,65,57,51,46,39,33,29,23,19,17,13,9,6,3,3,2,1,1,1,1,1,1,1,1,1],
        /* S */ [99,99,99,97,93,88,84,76,66,54,49,45,37,30,24,19,14,9,5,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
        /* C */ [99,98,94,91,84,77,66,59,53,45,37,33,27,19,15,9,6,4,3,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
    ];

    public function calculate(string $maskStr, string $latentStr): DiscScore
    {
        $maskStr   = substr($maskStr,   0, 48);
        $latentStr = substr($latentStr, 0, 48);

        $m  = [0, 0, 0, 0];
        $ls = [0, 0, 0, 0];

        for ($i = 0; $i <= 47; $i++) {
            for ($j = 0; $j <= 3; $j++) {
                $k = self::KEYS[$j][$i];
                if (isset($maskStr[$i])   && $maskStr[$i]   === $k) $m[$j]++;
                if (isset($latentStr[$i]) && $latentStr[$i] === $k) $ls[$j]++;
            }
        }

        $mp = [0, 0, 0, 0];
        $lp = [0, 0, 0, 0];

        for ($j = 0; $j <= 3; $j++) {
            $mp[$j] = self::NORMM[$j][max(1, $m[$j])  - 1];
            $lp[$j] = self::NORML[$j][max(1, $ls[$j]) - 1];
        }

        return new DiscScore($m, $ls, $mp, $lp);
    }
}
