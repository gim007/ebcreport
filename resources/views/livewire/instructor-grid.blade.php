<div>
    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search instructors..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($instructors as $instructor)
            <a href="{{ route('participant.courses', $instructor->ins_id) }}"
               class="block p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-blue-400 transition">
                <h3 class="font-semibold text-gray-800">
                    {{ $instructor->ins_fname }} {{ $instructor->ins_lname }}
                </h3>
                @if ($instructor->ins_email)
                    <p class="text-sm text-gray-500 mt-1">{{ $instructor->ins_email }}</p>
                @endif
            </a>
        @empty
            <p class="col-span-3 text-center text-gray-500 py-8">No instructors found.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $instructors->links() }}
    </div>
</div>
