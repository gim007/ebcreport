<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Organization;

class SelectionController extends Controller
{
    public function organizations()
    {
        return view('participant.select-organization');
    }

    public function instructors(int $orgId)
    {
        $org = Organization::where('uni_id', $orgId)
            ->where('is_hidden', false)
            ->firstOrFail();

        return view('participant.select-instructor', compact('org'));
    }

    public function courses(int $instructorId)
    {
        $instructor = Instructor::where('ins_id', $instructorId)
            ->where('is_hidden', false)
            ->where('admin_approval', 'Approved')
            ->firstOrFail();

        return view('participant.select-course', compact('instructor'));
    }
}
