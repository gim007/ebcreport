# DISC Scoring Engine — Reference Documentation

> **SOW reference:** R-11 (Scoring Engine Preservation — non-negotiable), R-43 (Scoring Engine documentation).
> **Source of truth:** `app/Services/DiscScoreCalculator.php` and `app/Services/DiscScore.php`.
> **Status:** Engine ported verbatim from legacy `score.php`. Byte-for-byte equivalence is verified by the test in `tests/Unit/DiscScoreCalculatorTest.php` driven by the fixture `tests/fixtures/disc_scores.json` (generate from the legacy DB; see "Regression test protocol" below).

---

## 1. Inputs

| Input | Shape | Source |
|---|---|---|
| `$maskStr` | 48-character string of digits `1-9` | `ebc_test_result.most_result_str` — the "Most like me" answer column |
| `$latentStr` | 48-character string of digits `1-9` | `ebc_test_result.least_result_str` — the "Least like me" answer column |

Each of the 48 positions corresponds to one question on the DISC assessment.

The 4 hardcoded answer keys (`KEYS[D]`, `KEYS[I]`, `KEYS[S]`, `KEYS[C]`) are 48-character digit strings used to score each dimension. They are reproduced byte-identically from the legacy file and **must not** be edited.

Two percentile lookup tables — `NORMM` (Mask, ascending) and `NORML` (Latent, descending) — map raw counts (1..36) to percentile (1..99) per dimension. These tables, including the deliberate non-monotonic indices `NORMM[1][19..22] = [89, 99, 93, 94]`, are also reproduced byte-identically. The non-monotonicity is preserved on purpose — it is part of the legacy norming and changing it would invalidate every report generated against the existing dataset.

---

## 2. Outputs — `DiscScore` value object

`DiscScoreCalculator::calculate()` returns an immutable `DiscScore` with four core arrays, each indexed `[D, I, S, C]`:

| Property | Type | Range | Meaning |
|---|---|---|---|
| `maskRaw` | `int[4]` | 0–12 (typical) | raw count of Mask positions matching each dimension's key |
| `latentRaw` | `int[4]` | 0–12 (typical) | raw count of Latent positions matching each dimension's key |
| `maskPercentile` | `int[4]` | 1–99 | NORMM lookup of `maskRaw` |
| `latentPercentile` | `int[4]` | 1–99 | NORML lookup of `latentRaw` |

Derived methods on `DiscScore`:

| Method | Returns | Used by |
|---|---|---|
| `shift(): int[4]` | `latentPercentile - maskPercentile` per dim | S-05 Shift chart |
| `maskPattern(): int` | bitmask 0..15 of mask dims above 50th percentile (D=8, I=4, S=2, C=1) | most paragraph methods |
| `latentPattern(): int` | same bitmask using latent percentiles | Struggles / Pressure paragraphs |
| `averagePattern(): int` | bitmask using `(mask+latent)/2 > 50` | Overview paragraph |
| `maskTensions(): int[6]` | pairwise mask diffs: I−D, S−D, C−D, S−I, C−I, C−S | S-17 Profile Tensions chart |
| `latentTensions(): int[6]` | pairwise latent diffs, same pair order | S-17 Profile Tensions chart |
| `dominantDimension(): int` | index 0..3 of highest mask percentile | conflict & working-with selection |
| `dominantLabel(): string` | `'D'` / `'I'` / `'S'` / `'C'` | report headings, snapshot tags |

---

## 3. The six scoring strategies (R-11, R-43)

The legacy engine documents six distinct strategies. All six are preserved in the port and map 1-to-1 to specific `DiscScore` accessors.

### Strategy 1 — Highest Score
Identifies the dominant DISC dimension by picking the index of the highest Mask percentile.
- **Implementation:** `DiscScore::dominantDimension()` / `DiscScore::dominantLabel()`
- **Used in:** S-15 Conflict Style, S-18–S-21 Working With (which sub-section emphasises is driven by the dominant dim), S-05 Snapshot tags.

### Strategy 2 — Configuration
Matches the profile to one of 16 standard patterns by which dimensions are "high" (above 50th percentile). The 4-bit configuration becomes the key into a switch/match selecting narrative content.
- **Implementation:** `DiscScore::maskPattern()` / `latentPattern()`
- **Bit layout:** `D = 8, I = 4, S = 2, C = 1`. Pattern `12` = D + I high. Pattern `5` = I + C high. Pattern `0` = none above 50.
- **Used in:** all Type-A paragraph methods (Motivation, Decision-Making, Strengths, Struggles, Connecting, Interpersonal, Motivating Factors).

### Strategy 3 — Differences
Pairwise comparisons between dimensions exceeding a threshold (`THRESH = 18` in the legacy stress-condition logic). Each crossing of the threshold triggers a distinct sentence in the output.
- **Implementation:** `DiscParagraphService::stressProfile()` performs all 12 ordered comparisons `($lp[a] - $lp[b]) > 18` plus the convergence test `abs($lp[0] - $lp[3]) < 15`.
- **Also visible in:** `DiscScore::maskTensions()` / `latentTensions()` (six unsigned-difference views used to plot the Profile Tensions chart in S-17).

### Strategy 4 — Bit-Flag Pattern
The 0..15 pattern integer is itself a feature. Used not only for paragraph selection but also to characterise the participant's overall configuration as one of the 16 standard DISC profiles.
- **Implementation:** `DiscScore::maskPattern()` returns the bit-flag integer directly.

### Strategy 5 — Multiple Threshold (Base-4 / Averaged)
Pattern computed from the average of Mask and Latent percentiles per dimension. Used by the Overview section which intentionally blends the public and private profiles.
- **Implementation:** `DiscScore::averagePattern()` — for each dim, includes the bit if `(maskPct + latentPct) / 2 > 50`.

### Strategy 6 — Mask vs Latent Differences (Shift)
Per-dimension signed difference `latent - mask`. Negative values mean the adapted style suppresses the natural tendency; positive values mean the natural self exceeds the adapted style.
- **Implementation:** `DiscScore::shift(): int[4]`
- **Used in:** S-05 Shift chart, S-16 How Others Perceive You.

---

## 4. NORMM and NORML lookup tables

Reproduced verbatim from the legacy file. Index is `rawCount - 1` (so a raw score of `0` is clamped to use index `0` via `max(1, $raw) - 1`).

### NORMM (Mask percentiles, ascending with raw score)
```
D: [1,4,7,9,12,16,22,25,38,45,52,58,62,65,68,72,76,83,85,87,89,91,93,94,95,96,97,97,98,98,99,99,99,99,99,99]
I: [1,3,6,10,17,26,35,42,48,57,64,71,76,81,84,88,90,93,95,96,89,99,93,94,95,96,97,97,98,98,99,99,99,99,99,99]
S: [1,1,2,3,6,11,14,21,24,29,35,45,50,57,64,70,77,82,87,91,94,96,98,99,99,99,99,99,99,99,99,99,99,99,99,99]
C: [1,1,2,2,3,5,7,13,16,19,22,29,38,44,54,58,65,73,79,85,88,92,97,98,99,99,99,99,99,99,99,99,99,99,99,99]
```

### NORML (Latent percentiles, descending with raw score)
A higher raw Latent score means a dimension is being suppressed in the natural ("least like me") response, so the percentile *decreases* with raw count.
```
D: [99,98,93,90,88,85,82,79,74,69,63,60,56,53,46,38,30,26,21,17,13,11,7,4,4,3,2,1,1,1,1,1,1,1,1,1]
I: [99,99,99,99,94,93,91,87,81,74,69,65,57,51,46,39,33,29,23,19,17,13,9,6,3,3,2,1,1,1,1,1,1,1,1,1]
S: [99,99,99,97,93,88,84,76,66,54,49,45,37,30,24,19,14,9,5,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
C: [99,98,94,91,84,77,66,59,53,45,37,33,27,19,15,9,6,4,3,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
```

### Known non-monotonicity (intentional)
`NORMM[I][19..22]` = `[89, 99, 93, 94]` — these four entries are NOT strictly ascending. This anomaly is reproduced from the legacy table verbatim. Do not "correct" it; the regression test suite will fail and every report ever produced will diverge from historical output.

---

## 5. Regression test protocol (R-11)

The non-negotiable acceptance criterion is byte-for-byte equivalence with the legacy engine on every known input.

### One-time fixture generation
A console command (`php artisan disc:generate-fixtures`) iterates a representative sample of `ebc_test_result` rows from the **legacy** `ebcdiscbeta2` database, runs the original `score.php` against each row's answer strings, and writes a JSON fixture containing the input + expected outputs:

```json
[
  {
    "most":       "923291199232331333113991191229121229129393292313",
    "least":      "212329231191923191331322922311932933391932919192",
    "mask_raw":   [11, 9, 7, 21],
    "latent_raw": [8, 4, 12, 24],
    "mask_pct":   [52, 57, 14, 99],
    "latent_pct": [74, 87, 9, 1]
  }
]
```

The fixture file lives at `tests/fixtures/disc_scores.json`.

### Test assertion
`tests/Unit/DiscScoreCalculatorTest::test_matches_legacy_output()` reads every fixture row and asserts that the new engine produces the same `mask_raw`, `latent_raw`, `mask_pct`, and `latent_pct` arrays — with `assertSame()`, so the types and order are also compared.

### Acceptance criterion
All fixture rows pass: `php artisan test --filter=DiscScoreCalculator` returns 100% green. **Until the fixture file exists, `test_matches_legacy_output` is skipped — the test runner will not falsely report success.**

---

## 6. Input sanitization and apostrophe handling (R-37 cross-reference)

The scoring engine itself accepts arbitrary strings and never executes user input — there is no SQL or shell exposure here. Strings shorter than 48 chars are clamped via `substr(..., 0, 48)` and missing positions count as no match. Strings longer than 48 chars are truncated to 48.

The 48-char answer-string format is generated server-side from controlled `M1..M48` / `L1..L48` form inputs in `SubmitTestAction`, so the engine never sees raw user-pasted content.

---

## 7. Reference: pattern integer → mnemonic

| Pattern | Dims above 50 | Common label |
|---|---|---|
| 0 | none | (low across the board) |
| 1 | C | C (Conscientious) |
| 2 | S | S (Steady) |
| 3 | S+C | SC |
| 4 | I | I (Influencer) |
| 5 | I+C | IC |
| 6 | I+S | IS |
| 7 | I+S+C | ISC |
| 8 | D | D (Dominant) |
| 9 | D+C | DC |
| 10 | D+S | DS |
| 11 | D+S+C | DSC |
| 12 | D+I | DI |
| 13 | D+I+C | DIC |
| 14 | D+I+S | DIS |
| 15 | all four high | DISC |

Use this table when reading the `match()` arms in `DiscParagraphService` — each numeric case corresponds to one row above.
