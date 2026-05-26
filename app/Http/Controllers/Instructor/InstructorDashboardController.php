<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;

        if (! $instructor) {
            abort(403, 'No instructor profile linked to this account.');
        }

        $courses = $instructor->courses()->withCount('testResults')->latest()->get();

        return view('instructor.dashboard', compact('instructor', 'courses'));
    }
}
