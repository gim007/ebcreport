{{-- Shared create/edit form partial --}}
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1" for="course_name">Course Name <span class="text-red-500">*</span></label>
        <input id="course_name" name="course_name" type="text" required
               value="{{ old('course_name', $course->course_name ?? '') }}"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('course_name') border-red-400 @enderror">
        @error('course_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1" for="course_code">Course Code</label>
            <input id="course_code" name="course_code" type="text"
                   value="{{ old('course_code', $course->course_code ?? '') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="term">Term / Semester</label>
            <input id="term" name="term" type="text"
                   value="{{ old('term', $course->term ?? '') }}"
                   placeholder="e.g. Fall 2026"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1" for="schedule_time">Schedule / Time</label>
            <input id="schedule_time" name="schedule_time" type="text"
                   value="{{ old('schedule_time', $course->schedule_time ?? '') }}"
                   placeholder="e.g. MWF 10:00–11:00am"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="course_price">Price ($)</label>
            <input id="course_price" name="course_price" type="number" step="0.01" min="0"
                   value="{{ old('course_price', $course->course_price ?? '') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('course_price') border-red-400 @enderror">
            @error('course_price')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1" for="paragraphs">Notes / Description</label>
        <textarea id="paragraphs" name="paragraphs" rows="4"
                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('paragraphs', $course->paragraphs ?? '') }}</textarea>
    </div>
</div>
