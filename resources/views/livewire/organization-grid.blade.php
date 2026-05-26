<div>
    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search organizations..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($organizations as $org)
            <a href="{{ route('participant.instructors', $org->uni_id) }}"
               class="block p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-blue-400 transition">
                @if ($org->hasMedia('logo'))
                    <img src="{{ $org->getFirstMediaUrl('logo') }}"
                         alt="{{ $org->uni_name }}"
                         class="h-12 w-auto mb-3 object-contain">
                @endif
                <h3 class="font-semibold text-gray-800">{{ $org->uni_name }}</h3>
            </a>
        @empty
            <p class="col-span-3 text-center text-gray-500 py-8">No organizations found.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $organizations->links() }}
    </div>
</div>
