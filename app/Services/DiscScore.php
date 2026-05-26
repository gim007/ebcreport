<?php

namespace App\Services;

final readonly class DiscScore
{
    public function __construct(
        public array $maskRaw,           // int[4] raw Mask counts      [D, I, S, C]
        public array $latentRaw,         // int[4] raw Latent counts    [D, I, S, C]
        public array $maskPercentile,    // int[4] Mask percentiles     [D, I, S, C]
        public array $latentPercentile,  // int[4] Latent percentiles   [D, I, S, C]
    ) {}

    /** Shift per dimension: Latent − Mask. Displayed in the Shift chart (S-05). */
    public function shift(): array
    {
        return [
            $this->latentPercentile[0] - $this->maskPercentile[0],
            $this->latentPercentile[1] - $this->maskPercentile[1],
            $this->latentPercentile[2] - $this->maskPercentile[2],
            $this->latentPercentile[3] - $this->maskPercentile[3],
        ];
    }

    /** Bitmask of Mask dimensions above 50th percentile (paragraph selection). */
    public function maskPattern(): int
    {
        $p = 0;
        foreach ($this->maskPercentile as $i => $v) {
            if ($v > 50) {
                $p += (int) pow(2, 3 - $i);
            }
        }
        return $p;
    }

    /** Same bitmask using Latent percentiles (Blind Spots / Struggles section). */
    public function latentPattern(): int
    {
        $p = 0;
        foreach ($this->latentPercentile as $i => $v) {
            if ($v > 50) {
                $p += (int) pow(2, 3 - $i);
            }
        }
        return $p;
    }

    /** Pattern using average of Mask and Latent percentiles (Overview section). */
    public function averagePattern(): int
    {
        $p = 0;
        for ($i = 0; $i <= 3; $i++) {
            if (($this->maskPercentile[$i] + $this->latentPercentile[$i]) / 2 > 50) {
                $p += (int) pow(2, 3 - $i);
            }
        }
        return $p;
    }

    /** 6 pairwise differences for the Profile Tensions chart — Mask side. */
    public function maskTensions(): array
    {
        $mp = $this->maskPercentile;
        return [
            $mp[1] - $mp[0], // I − D
            $mp[2] - $mp[0], // S − D
            $mp[3] - $mp[0], // C − D
            $mp[2] - $mp[1], // S − I
            $mp[3] - $mp[1], // C − I
            $mp[3] - $mp[2], // C − S
        ];
    }

    /** 6 pairwise differences for the Profile Tensions chart — Latent side. */
    public function latentTensions(): array
    {
        $lp = $this->latentPercentile;
        return [
            $lp[1] - $lp[0],
            $lp[2] - $lp[0],
            $lp[3] - $lp[0],
            $lp[2] - $lp[1],
            $lp[3] - $lp[1],
            $lp[3] - $lp[2],
        ];
    }

    /** Dominant dimension index by highest Mask percentile (0=D, 1=I, 2=S, 3=C). */
    public function dominantDimension(): int
    {
        return array_keys($this->maskPercentile, max($this->maskPercentile))[0];
    }

    /** Dominant dimension as a letter ('D', 'I', 'S', or 'C'). */
    public function dominantLabel(): string
    {
        return ['D', 'I', 'S', 'C'][$this->dominantDimension()];
    }
}
