<?php

namespace App\Services;

/**
 * Generates inline SVG charts for the DISC report.
 * Pure PHP — no Chromium, no JavaScript required.
 * Safe to embed directly in DomPDF output.
 */
class DiscChartService
{
    // Mockup colours (R-20, R-48): D=green, I=red, S=blue, C=gold
    private const COLORS = [
        'D' => '#2e7d32', // green
        'I' => '#c62828', // red
        'S' => '#1565c0', // blue
        'C' => '#f9a825', // gold
    ];

    private const LABELS = ['D', 'I', 'S', 'C'];

    /**
     * Vertical bar chart for a single profile (Mask or Latent). Per-mockup layout:
     * 0/25/50/75/100 y-axis gridlines, D/I/S/C bars in mockup colours, value labels above bars.
     */
    public function verticalProfileChart(array $values, string $title = ''): string
    {
        $w        = 220;
        $h        = 260;
        $padTop   = 24;
        $padBot   = 36;
        $padLeft  = 30;
        $padRight = 14;
        $plotW    = $w - $padLeft - $padRight;
        $plotH    = $h - $padTop - $padBot;
        $barGap   = 10;
        $barW     = (int) (($plotW - ($barGap * (count(self::LABELS) + 1))) / count(self::LABELS));

        $svg  = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$w}\" height=\"{$h}\" font-family=\"sans-serif\" font-size=\"10\">";

        // Y-axis gridlines + labels (0, 25, 50, 75, 100)
        foreach ([0, 25, 50, 75, 100] as $tick) {
            $y = $padTop + $plotH - (int) ($plotH * ($tick / 100));
            $svg .= "<line x1=\"{$padLeft}\" y1=\"{$y}\" x2=\"" . ($padLeft + $plotW) . "\" y2=\"{$y}\" stroke=\"#e5e7eb\" stroke-width=\"1\"/>";
            $svg .= "<text x=\"" . ($padLeft - 4) . "\" y=\"" . ($y + 3) . "\" text-anchor=\"end\" fill=\"#6b7280\">{$tick}</text>";
        }

        // Bars
        foreach (self::LABELS as $i => $dim) {
            $v     = (int) max(0, min(100, $values[$i] ?? 0));
            $color = self::COLORS[$dim];
            $x     = $padLeft + $barGap + $i * ($barW + $barGap);
            $bh    = (int) ($plotH * ($v / 100));
            $y     = $padTop + $plotH - $bh;

            $svg .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$barW}\" height=\"{$bh}\" fill=\"{$color}\"/>";
            $svg .= "<text x=\"" . ($x + $barW / 2) . "\" y=\"" . ($y - 4) . "\" text-anchor=\"middle\" fill=\"#1f2937\" font-weight=\"bold\">{$v}</text>";
            $svg .= "<text x=\"" . ($x + $barW / 2) . "\" y=\"" . ($padTop + $plotH + 16) . "\" text-anchor=\"middle\" fill=\"{$color}\" font-weight=\"bold\" font-size=\"13\">{$dim}</text>";
        }

        if ($title !== '') {
            $svg .= "<text x=\"" . ($w / 2) . "\" y=\"14\" text-anchor=\"middle\" fill=\"#111827\" font-weight=\"bold\" font-size=\"12\">{$title}</text>";
        }

        $svg .= '</svg>';
        return $svg;
    }

    /**
     * Bipolar vertical bar chart for the Shift (Latent − Mask) per dimension.
     * Center axis at 0, bars extend up for positive, down for negative.
     */
    public function shiftChart(array $shift): string
    {
        $w        = 220;
        $h        = 260;
        $padTop   = 24;
        $padBot   = 36;
        $padLeft  = 30;
        $padRight = 14;
        $plotW    = $w - $padLeft - $padRight;
        $plotH    = $h - $padTop - $padBot;
        $center   = $padTop + (int) ($plotH / 2);
        $barGap   = 10;
        $barW     = (int) (($plotW - ($barGap * (count(self::LABELS) + 1))) / count(self::LABELS));
        $halfH    = $plotH / 2;

        $svg  = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$w}\" height=\"{$h}\" font-family=\"sans-serif\" font-size=\"10\">";

        // Y-axis gridlines: -100, -50, 0, +50, +100
        foreach ([-100, -50, 0, 50, 100] as $tick) {
            $y = $center - (int) ($halfH * ($tick / 100));
            $stroke = $tick === 0 ? '#9ca3af' : '#e5e7eb';
            $svg .= "<line x1=\"{$padLeft}\" y1=\"{$y}\" x2=\"" . ($padLeft + $plotW) . "\" y2=\"{$y}\" stroke=\"{$stroke}\" stroke-width=\"1\"/>";
            $label = $tick > 0 ? "+{$tick}" : (string) $tick;
            $svg .= "<text x=\"" . ($padLeft - 4) . "\" y=\"" . ($y + 3) . "\" text-anchor=\"end\" fill=\"#6b7280\">{$label}</text>";
        }

        foreach (self::LABELS as $i => $dim) {
            $v     = (int) max(-100, min(100, $shift[$i] ?? 0));
            $color = self::COLORS[$dim];
            $x     = $padLeft + $barGap + $i * ($barW + $barGap);
            $bh    = (int) ($halfH * abs($v) / 100);
            $y     = $v >= 0 ? $center - $bh : $center;

            $svg .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$barW}\" height=\"{$bh}\" fill=\"{$color}\"/>";
            $labelY = $v >= 0 ? $y - 4 : $y + $bh + 11;
            $sign   = $v > 0 ? '+' : '';
            $svg .= "<text x=\"" . ($x + $barW / 2) . "\" y=\"{$labelY}\" text-anchor=\"middle\" fill=\"#1f2937\" font-weight=\"bold\">{$sign}{$v}</text>";
            $svg .= "<text x=\"" . ($x + $barW / 2) . "\" y=\"" . ($padTop + $plotH + 16) . "\" text-anchor=\"middle\" fill=\"{$color}\" font-weight=\"bold\" font-size=\"13\">{$dim}</text>";
        }

        $svg .= '</svg>';
        return $svg;
    }

    /**
     * Static four-quadrant DISC compass diagram for cover and S-04.
     * D=green (top-left), I=red (top-right), S=blue (bottom-right), C=gold (bottom-left).
     */
    public function discCompass(int $size = 180): string
    {
        $cx = $size / 2;
        $cy = $size / 2;
        $r  = $size * 0.45;

        // Four quadrant paths (top-left D, top-right I, bottom-right S, bottom-left C)
        $arc = function (float $startA, float $endA) use ($cx, $cy, $r): string {
            $x1 = $cx + $r * cos(deg2rad($startA));
            $y1 = $cy + $r * sin(deg2rad($startA));
            $x2 = $cx + $r * cos(deg2rad($endA));
            $y2 = $cy + $r * sin(deg2rad($endA));
            return "M{$cx},{$cy} L{$x1},{$y1} A{$r},{$r} 0 0,1 {$x2},{$y2} Z";
        };

        $svg  = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$size}\" height=\"{$size}\" viewBox=\"0 0 {$size} {$size}\">";
        // Quadrants — angles measured clockwise from positive x-axis (3 o'clock)
        $svg .= '<path d="' . $arc(180, 270) . '" fill="' . self::COLORS['D'] . '"/>'; // top-left
        $svg .= '<path d="' . $arc(270, 360) . '" fill="' . self::COLORS['I'] . '"/>'; // top-right
        $svg .= '<path d="' . $arc(0,   90)  . '" fill="' . self::COLORS['S'] . '"/>'; // bottom-right
        $svg .= '<path d="' . $arc(90,  180) . '" fill="' . self::COLORS['C'] . '"/>'; // bottom-left

        // Compass needle pointing slightly down-right (decorative, matches mockup)
        $needleR = $r * 0.85;
        $nx = $cx + $needleR * cos(deg2rad(95));
        $ny = $cy + $needleR * sin(deg2rad(95));
        $svg .= "<line x1=\"{$cx}\" y1=\"{$cy}\" x2=\"{$nx}\" y2=\"{$ny}\" stroke=\"#1f2937\" stroke-width=\"2\"/>";
        $svg .= "<circle cx=\"{$cx}\" cy=\"{$cy}\" r=\"" . ($size * 0.045) . "\" fill=\"#f3f4f6\" stroke=\"#1f2937\" stroke-width=\"1\"/>";

        $svg .= '</svg>';
        return $svg;
    }

    /**
     * Horizontal four-color bar strip used in header and footer (R-47).
     * green / red / blue / gold with a thin dark edge.
     */
    public function colorBarStrip(int $width = 612, int $barHeight = 6, int $edgeHeight = 4): string
    {
        $w = $width;
        $segW = (int) ($w / 4);
        $svg  = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$w}\" height=\"" . ($barHeight + $edgeHeight) . "\">";
        $svg .= "<rect x=\"0\" y=\"0\" width=\"{$segW}\" height=\"{$barHeight}\" fill=\"" . self::COLORS['D'] . "\"/>";
        $svg .= "<rect x=\"{$segW}\" y=\"0\" width=\"{$segW}\" height=\"{$barHeight}\" fill=\"" . self::COLORS['I'] . "\"/>";
        $svg .= "<rect x=\"" . ($segW * 2) . "\" y=\"0\" width=\"{$segW}\" height=\"{$barHeight}\" fill=\"" . self::COLORS['S'] . "\"/>";
        $svg .= "<rect x=\"" . ($segW * 3) . "\" y=\"0\" width=\"" . ($w - $segW * 3) . "\" height=\"{$barHeight}\" fill=\"" . self::COLORS['C'] . "\"/>";
        $svg .= "<rect x=\"0\" y=\"{$barHeight}\" width=\"{$w}\" height=\"{$edgeHeight}\" fill=\"#111827\"/>";
        $svg .= '</svg>';
        return $svg;
    }

    /**
     * Horizontal bar chart showing Mask (solid) and Latent (hatched) percentiles.
     * Width ~480px, height ~200px.
     */
    public function profileBars(DiscScore $score): string
    {
        $mp = $score->maskPercentile;
        $lp = $score->latentPercentile;

        $w      = 480;
        $labelW = 30;
        $barW   = $w - $labelW - 20;  // max bar pixel width = 100%
        $rowH   = 40;
        $padTop = 30;
        $h      = $padTop + count(self::LABELS) * $rowH + 10;

        $svg  = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$w}\" height=\"{$h}\" font-family=\"sans-serif\" font-size=\"11\">";
        $svg .= '<defs>';
        $svg .= '<pattern id="hatch" patternUnits="userSpaceOnUse" width="4" height="4"><path d="M-1,1 l2,-2 M0,4 l4,-4 M3,5 l2,-2" stroke="#999" stroke-width="1"/></pattern>';
        $svg .= '</defs>';

        // Column headers
        $svg .= "<text x=\"{$labelW}\" y=\"20\" fill=\"#374151\" font-weight=\"bold\">0</text>";
        $svg .= '<text x="' . ($labelW + $barW / 2) . '" y="20" text-anchor="middle" fill="#374151" font-weight="bold">50</text>';
        $svg .= '<text x="' . ($labelW + $barW) . '" y="20" text-anchor="end" fill="#374151" font-weight="bold">100</text>';
        // 50th-percentile guide line
        $x50 = $labelW + ($barW * 0.5);
        $svg .= "<line x1=\"{$x50}\" y1=\"{$padTop}\" x2=\"{$x50}\" y2=\"{$h}\" stroke=\"#d1d5db\" stroke-width=\"1\" stroke-dasharray=\"4,2\"/>";

        foreach (self::LABELS as $i => $dim) {
            $y     = $padTop + $i * $rowH;
            $color = self::COLORS[$dim];
            $mPx   = max(2, (int) round($mp[$i] / 100 * $barW));
            $lPx   = max(2, (int) round($lp[$i] / 100 * $barW));
            $barH  = 12;
            $gap   = 4;

            // Dim label
            $svg .= "<text x=\"" . ($labelW - 6) . "\" y=\"" . ($y + $barH + 2) . "\" text-anchor=\"end\" fill=\"{$color}\" font-weight=\"bold\">{$dim}</text>";
            // Mask bar (solid)
            $svg .= "<rect x=\"{$labelW}\" y=\"{$y}\" width=\"{$mPx}\" height=\"{$barH}\" fill=\"{$color}\" rx=\"2\"/>";
            $svg .= "<text x=\"" . ($labelW + $mPx + 3) . "\" y=\"" . ($y + $barH - 1) . "\" fill=\"{$color}\">{$mp[$i]}</text>";
            // Latent bar (hatched)
            $svg .= "<rect x=\"{$labelW}\" y=\"" . ($y + $barH + $gap) . "\" width=\"{$lPx}\" height=\"{$barH}\" fill=\"url(#hatch)\" stroke=\"{$color}\" stroke-width=\"1\" rx=\"2\"/>";
            $svg .= "<text x=\"" . ($labelW + $lPx + 3) . "\" y=\"" . ($y + $barH * 2 + $gap - 1) . "\" fill=\"#6b7280\">{$lp[$i]}</text>";
        }

        // Legend
        $ly = $h - 5;
        $svg .= "<rect x=\"{$labelW}\" y=\"" . ($ly - 8) . "\" width=\"10\" height=\"8\" fill=\"#6b7280\"/><text x=\"" . ($labelW + 14) . "\" y=\"{$ly}\" fill=\"#6b7280\">Mask</text>";
        $svg .= "<rect x=\"" . ($labelW + 60) . "\" y=\"" . ($ly - 8) . "\" width=\"10\" height=\"8\" fill=\"url(#hatch)\" stroke=\"#6b7280\" stroke-width=\"1\"/><text x=\"" . ($labelW + 74) . "\" y=\"{$ly}\" fill=\"#6b7280\">Latent</text>";

        $svg .= '</svg>';
        return $svg;
    }

    /**
     * Six-pair tension chart showing Mask (top) and Latent (bottom) pairwise differences.
     * Width ~480px.
     */
    public function tensionBars(DiscScore $score): string
    {
        $mTens = $score->maskTensions();
        $lTens = $score->latentTensions();
        $pairs = ['I−D', 'S−D', 'C−D', 'S−I', 'C−I', 'C−S'];

        $w       = 480;
        $labelW  = 44;
        $axisW   = $w - $labelW - 20;
        $rowH    = 40;
        $padTop  = 30;
        $h       = $padTop + count($pairs) * $rowH + 20;
        $origin  = $labelW + (int) ($axisW / 2);
        $scale   = $axisW / 200; // ±100 maps to full width

        $svg  = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$w}\" height=\"{$h}\" font-family=\"sans-serif\" font-size=\"11\">";
        $svg .= '<defs>';
        $svg .= '<pattern id="hatch2" patternUnits="userSpaceOnUse" width="4" height="4"><path d="M-1,1 l2,-2 M0,4 l4,-4 M3,5 l2,-2" stroke="#999" stroke-width="1"/></pattern>';
        $svg .= '</defs>';

        // Headers
        $svg .= '<text x="' . ($origin - 2) . '" y="20" text-anchor="middle" fill="#374151" font-weight="bold">0</text>';
        $svg .= '<text x="' . ($labelW) . '" y="20" text-anchor="start" fill="#374151">−</text>';
        $svg .= '<text x="' . ($w - 20) . '" y="20" text-anchor="end" fill="#374151">+</text>';
        // Center guide
        $svg .= "<line x1=\"{$origin}\" y1=\"{$padTop}\" x2=\"{$origin}\" y2=\"{$h}\" stroke=\"#d1d5db\" stroke-width=\"1\" stroke-dasharray=\"4,2\"/>";

        foreach ($pairs as $idx => $label) {
            $y    = $padTop + $idx * $rowH;
            $barH = 12;
            $gap  = 4;

            $svg .= "<text x=\"" . ($labelW - 4) . "\" y=\"" . ($y + $barH - 1) . "\" text-anchor=\"end\" fill=\"#374151\">{$label}</text>";

            // Mask bar
            $mv = (int) round($mTens[$idx] * $scale);
            if ($mv >= 0) {
                $svg .= "<rect x=\"{$origin}\" y=\"{$y}\" width=\"{$mv}\" height=\"{$barH}\" fill=\"#2563eb\" rx=\"2\"/>";
            } else {
                $svg .= "<rect x=\"" . ($origin + $mv) . "\" y=\"{$y}\" width=\"" . abs($mv) . "\" height=\"{$barH}\" fill=\"#dc2626\" rx=\"2\"/>";
            }
            $svg .= "<text x=\"" . ($origin + $mv + ($mv >= 0 ? 3 : -3)) . "\" y=\"" . ($y + $barH - 1) . "\" text-anchor=\"" . ($mv >= 0 ? 'start' : 'end') . "\" fill=\"#374151\">{$mTens[$idx]}</text>";

            // Latent bar
            $lv = (int) round($lTens[$idx] * $scale);
            $ly2 = $y + $barH + $gap;
            if ($lv >= 0) {
                $svg .= "<rect x=\"{$origin}\" y=\"{$ly2}\" width=\"{$lv}\" height=\"{$barH}\" fill=\"url(#hatch2)\" stroke=\"#2563eb\" stroke-width=\"1\" rx=\"2\"/>";
            } else {
                $svg .= "<rect x=\"" . ($origin + $lv) . "\" y=\"{$ly2}\" width=\"" . abs($lv) . "\" height=\"{$barH}\" fill=\"url(#hatch2)\" stroke=\"#dc2626\" stroke-width=\"1\" rx=\"2\"/>";
            }
        }

        // Legend
        $ly = $h - 5;
        $svg .= "<rect x=\"{$labelW}\" y=\"" . ($ly - 8) . "\" width=\"10\" height=\"8\" fill=\"#2563eb\"/><text x=\"" . ($labelW + 14) . "\" y=\"{$ly}\" fill=\"#6b7280\">Mask</text>";
        $svg .= "<rect x=\"" . ($labelW + 60) . "\" y=\"" . ($ly - 8) . "\" width=\"10\" height=\"8\" fill=\"url(#hatch2)\" stroke=\"#2563eb\" stroke-width=\"1\"/><text x=\"" . ($labelW + 74) . "\" y=\"{$ly}\" fill=\"#6b7280\">Latent</text>";

        $svg .= '</svg>';
        return $svg;
    }
}
