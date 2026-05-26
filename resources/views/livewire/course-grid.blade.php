<div>
    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search courses..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($courses as $course)
            <a href="{{ route('participant.register', $course->course_id) }}"
               class="block p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-blue-400 transition">
                <h3 class="font-semibold text-gray-800">{{ $course->course_name }}</h3>
                @if (isset($course->course_date))
                    <p class="text-sm text-gray-500 mt-1">{{ $course->course_date }}</p>
                @endif
            </a>
        @empty
            <p class="col-span-3 text-center text-gray-500 py-8">No courses found.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</div>
