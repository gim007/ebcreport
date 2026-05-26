<?php

namespace App\Http\Controllers\Participant;

use App\Actions\SubmitTestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitTestRequest;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function show()
    {
        $participant = Auth::user()->participant;

        if (! $participant || $participant->tot_credit < 1) {
            return redirect()->route('participant.account')
                ->withErrors(['access' => 'You need a credit to take the assessment.']);
        }

        $existing = TestResult::where('stud_id', $participant->stud_id)
            ->whereNotNull('most_result_str')
            ->latest('result_date')
            ->first();

        return view('participant.test', compact('participant', 'existing'));
    }

    public function submit(SubmitTestRequest $request, SubmitTestAction $action)
    {
        $participant = Auth::user()->participant;

        if (! $participant || $participant->tot_credit < 1) {
            abort(403, 'No credits available.');
        }

        $result = TestResult::firstOrCreate(
            ['stud_id' => $participant->stud_id, 'most_result_str' => null],
            ['stud_id' => $participant->stud_id]
        );

        $action->execute($result, $request->validated(), (int) $request->focus);

        return redirect()->route('participant.report.show', $result->test_result_id)
            ->with('status', 'Assessment submitted. Your report is being generated.');
    }
}
