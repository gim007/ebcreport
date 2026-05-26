<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class InstructorCourseController extends Controller
{
    private function instructorOrFail()
    {
        $instructor = Auth::user()->instructor;
        abort_if(! $instructor, 403, 'No instructor profile found.');
        return $instructor;
    }

    public function index()
    {
        $instructor = $this->instructorOrFail();
        $courses    = $instructor->courses()->withCount('testResults')->latest()->paginate(20);

        return view('instructor.courses.index', compact('instructor', 'courses'));
    }

    public function create()
    {
        $instructor = $this->instructorOrFail();
        return view('instructor.courses.create', compact('instructor'));
    }

    public function store(StoreCourseRequest $request)
    {
        $instructor = $this->instructorOrFail();

        $instructor->courses()->create($request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('status', 'Course created successfully.');
    }

    public function edit(int $courseId)
    {
        $instructor = $this->instructorOrFail();
        $course     = $instructor->courses()->findOrFail($courseId);

        return view('instructor.courses.edit', compact('instructor', 'course'));
    }

    public function update(StoreCourseRequest $request, int $courseId)
    {
        $instructor = $this->instructorOrFail();
        $course     = $instructor->courses()->findOrFail($courseId);

        $course->update($request->validated());

        return redirect()->route('instructor.courses.index')
            ->with('status', 'Course updated successfully.');
    }

    public function destroy(int $courseId)
    {
        $instructor = $this->instructorOrFail();
        $course     = $instructor->courses()->findOrFail($courseId);

        if ($course->testResults()->exists()) {
            return back()->withErrors(['delete' => 'Cannot delete a course that has assessment results.']);
        }

        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('status', 'Course deleted.');
    }
}
