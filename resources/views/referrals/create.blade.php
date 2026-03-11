<x-app-layout>
    <div class="max-w-4xl mx-auto px-8 py-10">

        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('referrals.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Referrals
            </a>
        </div>

        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Create Supportive Intervention Referral</h1>
        <p class="text-slate-600 font-medium mb-8">Link a student to professional counseling support based on registry or disciplinary findings.</p>

        <form action="{{ route('referrals.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- LEFT COLUMN: Student Selection -->
                <div class="bg-white border-2 border-slate-200 rounded-3xl p-8 h-fit">
                    <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Student Selection</h2>

                    <div>
                        <label for="student_id" class="block text-sm font-bold text-slate-700 mb-3">
                            Select Student from Registry
                        </label>
                        <select
                            id="student_id"
                            name="student_id"
                            required
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl bg-white text-slate-900 font-semibold focus:outline-none focus:border-[#004d32] focus:ring-2 focus:ring-[#004d32]/20 transition-all"
                            onchange="updateStudentInfo()">
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" data-id-number="{{ $student->id_number }}" data-program="{{ $student->program_code }}">
                                    {{ $student->name }} ({{ $student->id_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="text-red-600 text-sm font-semibold mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="student-info" class="mt-6 p-4 bg-slate-50 border-2 border-slate-200 rounded-xl hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">ID Number</span>
                                <span id="display-id" class="block font-bold text-slate-900"></span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Program</span>
                                <span id="display-program" class="block font-bold text-slate-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Referral Details -->
                <div class="space-y-8">

                    <!-- Case Reference -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-8">
                        <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Referral Context</h2>

                        <div>
                            <label for="case_reference" class="block text-sm font-bold text-slate-700 mb-3">
                                Case Reference (Optional)
                            </label>
                            <input
                                type="text"
                                id="case_reference"
                                name="case_reference"
                                placeholder="e.g., LOST-001, VIO-123, INC-045"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl bg-slate-50 text-slate-900 font-semibold focus:outline-none focus:border-[#004d32] focus:ring-2 focus:ring-[#004d32]/20 transition-all"
                                maxlength="100">
                            <p class="text-xs text-slate-500 font-medium mt-2">Link to existing registry or incident record</p>
                            @error('case_reference')
                                <p class="text-red-600 text-sm font-semibold mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Support Urgency -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-8">
                        <h2 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Support Urgency</h2>

                        <div class="space-y-3">
                            <label class="flex items-start gap-4 p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-[#004d32] hover:bg-slate-50 transition-all">
                                <input type="radio" name="support_urgency" value="Standard" class="mt-1" required>
                                <div>
                                    <p class="font-bold text-slate-900">Standard Support</p>
                                    <p class="text-sm text-slate-600">Routine counseling intervention</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-4 p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-[#004d32] hover:bg-slate-50 transition-all">
                                <input type="radio" name="support_urgency" value="Priority" class="mt-1" required>
                                <div>
                                    <p class="font-bold text-slate-900">Priority Support</p>
                                    <p class="text-sm text-slate-600">High-priority intervention recommended</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-4 p-4 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-[#004d32] hover:bg-slate-50 transition-all">
                                <input type="radio" name="support_urgency" value="Immediate" class="mt-1" required>
                                <div>
                                    <p class="font-bold text-slate-900">Immediate Support</p>
                                    <p class="text-sm text-slate-600">Urgent counseling required immediately</p>
                                </div>
                            </label>
                        </div>
                        @error('support_urgency')
                            <p class="text-red-600 text-sm font-semibold mt-3">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Administrative Observations -->
            <div class="bg-slate-50 border-2 border-slate-200 rounded-3xl p-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-10 h-10 bg-[#004d32] rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Registry Intelligence Observations</h2>
                        <p class="text-sm font-medium text-slate-600 mt-1">Provide context for the guidance office</p>
                    </div>
                </div>

                <div>
                    <label for="administrative_observations" class="block text-sm font-bold text-slate-700 mb-3">
                        Administrative Notes
                    </label>
                    <textarea
                        id="administrative_observations"
                        name="administrative_observations"
                        required
                        rows="8"
                        placeholder="Provide context from registry findings, behavioral observations, or supporting details for the guidance office..."
                        class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl bg-white text-slate-900 font-semibold focus:outline-none focus:border-[#004d32] focus:ring-2 focus:ring-[#004d32]/20 transition-all"
                        minlength="10"></textarea>
                    <p class="text-xs text-slate-500 font-medium mt-2">Minimum 10 characters required</p>
                    @error('administrative_observations')
                        <p class="text-red-600 text-sm font-semibold mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('referrals.index') }}"
                    class="flex-1 px-8 py-4 border-2 border-slate-300 text-slate-700 font-bold text-center rounded-2xl hover:bg-slate-100 transition-all">
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 px-8 py-4 bg-[#004d32] text-white font-bold rounded-2xl hover:bg-green-800 transition-all shadow-md">
                    Create Referral
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateStudentInfo() {
            const select = document.getElementById('student_id');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('student-info');

            if (select.value) {
                document.getElementById('display-id').textContent = option.dataset.idNumber;
                document.getElementById('display-program').textContent = option.dataset.program || 'N/A';
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
