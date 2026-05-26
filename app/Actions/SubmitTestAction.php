<?php

namespace App\Actions;

use App\Jobs\GenerateAndEmailReportJob;
use App\Models\TestResult;

class SubmitTestAction
{
    public function execute(TestResult $result, array $answers, int $focus): void
    {
        $most = $least = '';
        for ($i = 1; $i <= 48; $i++) {
            $most  .= $answers["M{$i}"] ?? '';
            $least .= $answers["L{$i}"] ?? '';
        }

        $result->update([
            'most_result_str'  => $most,
            'least_result_str' => $least,
            'focus'            => $focus,
            'result_date'      => now(),
        ]);

        // Deduct one credit from the participant
        $result->participant->decrement('tot_credit');

        // R-24: auto-generate and email PDF immediately on completion
        GenerateAndEmailReportJob::dispatch($result);
    }
}
