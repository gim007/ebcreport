<?php

namespace App\Livewire;

use App\Models\Organization;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

// Replaces student_select_school.php — terminology: "Organization" (R-36)
class OrganizationGrid extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $organizations = Organization::query()
            ->where('is_hidden', false)
            ->when($this->search, fn ($q) => $q->where('uni_name', 'like', "%{$this->search}%"))
            ->orderBy('uni_name')
            ->paginate(20);

        return view('livewire.organization-grid', compact('organizations'));
    }
}
