<?php

namespace App\Livewire;

use App\Models\Instructor;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Replaces student_select_instructor.php
class InstructorGrid extends Component
{
    use WithPagination;

    public int    $orgId  = 0;
    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $instructors = Instructor::query()
            ->where('uni_id', $this->orgId)
            ->where('is_hidden', false)
            ->where('admin_approval', 'Approved')
            ->when($this->search, fn ($q) => $q->where(function ($q2) {
                $q2->where('ins_fname', 'like', "%{$this->search}%")
                   ->orWhere('ins_lname', 'like', "%{$this->search}%");
            }))
            ->orderBy('ins_lname')
            ->paginate(20);

        return view('livewire.instructor-grid', compact('instructors'));
    }
}
