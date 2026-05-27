<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Replaces student_select_course.php
class CourseGrid extends Component
{
    use WithPagination;

    public int    $instructorId = 0;
    #[Url]
    public string $search       = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $courses = Course::query()
            ->where('inst_id', $this->instructorId)
            ->where('is_hidden', false)                // R-38 pattern
            ->where(fn ($q) => $q                      // hide past-expiry courses
                ->whereNull('expiry_date')
                ->orWhere('expiry_date', '>=', today()))
            ->when($this->search, fn ($q) => $q->where('course_name', 'like', "%{$this->search}%"))
            ->orderBy('course_name')
            ->paginate(20);

        return view('livewire.course-grid', compact('courses'));
    }
}
