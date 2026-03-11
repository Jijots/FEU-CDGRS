<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('referrals.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Referrals
            </a>
        </div>

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Supportive Intervention Referral</h1>
                <p class="text-slate-600 font-medium mt-1">
                    Referred on {{ $referral->created_at->format('M d, Y') }}
                </p>
            </div>
            @if($referral->administrative_status === 'Draft')
                <span class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm border-2 border-slate-200 uppercase tracking-wide">Draft</span>
            @elseif($referral->administrative_status === 'Forwarded to Guidance')
                <span class="px-4 py-2 bg-blue-100 text-blue-700 font-bold rounded-xl text-sm border-2 border-blue-200 uppercase tracking-wide">Forwarded to Guidance</span>
            @elseif($referral->administrative_status === 'Scheduled')
                <span class="px-4 py-2 bg-green-100 text-green-700 font-bold rounded-xl text-sm border-2 border-green-200 uppercase tracking-wide">Scheduled</span>
            @else
                <span class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-xl text-sm border-2 border-slate-300 uppercase tracking-wide">Resolved</span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN: Student & Metadata -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Student Card -->
                <div class="bg-white border-2 border-slate-200 rounded-3xl p-8">
                    <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Student Information</h3>

                    <div class="space-y-6">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Full Name</span>
                            <span class="block text-base font-bold text-slate-900">{{ $referral->student->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Student ID</span>
                            <span class="block text-base font-bold text-slate-900">{{ $referral->student->id_number }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Program</span>
                            <span class="block text-base font-bold text-slate-900">{{ $referral->student->program_code ?? 'N/A' }}</span>
                        </div>
                        <a href="{{ route('students.show', $referral->student->id) }}"
                            class="inline-flex items-center gap-2 px-4 py-3 bg-slate-50 hover:bg-slate-100 text-[#004d32] font-bold rounded-xl transition-all mt-2">
                            View Full Record
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Referral Metadata -->
                <div class="bg-white border-2 border-slate-200 rounded-3xl p-8">
                    <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Referral Details</h3>

                    <div class="space-y-6">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Case Reference</span>
                            <span class="block text-base font-bold text-slate-900">{{ $referral->case_reference ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Support Urgency</span>
                            @if($referral->support_urgency === 'Standard')
                                <span class="inline-block px-3 py-1 bg-slate-200 text-slate-600 font-bold rounded-lg text-sm">Standard</span>
                            @elseif($referral->support_urgency === 'Priority')
                                <span class="inline-block px-3 py-1 bg-amber-200 text-amber-700 font-bold rounded-lg text-sm">Priority</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-red-200 text-red-700 font-bold rounded-lg text-sm">Immediate</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Created By</span>
                            <span class="block text-base font-bold text-slate-900">{{ $referral->referrer->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Created</span>
                            <span class="block text-base font-bold text-slate-900">{{ $referral->created_at->format('M d, Y · H:i') }}</span>
                        </div>
                        @if($referral->scheduled_date)
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Scheduled Date</span>
                                <span class="block text-base font-bold text-slate-900">{{ $referral->scheduled_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Observations & Workflow -->
            <div class="lg:col-span-2 space-y-8">

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
                            <p class="text-sm font-medium text-slate-600 mt-0.5">Context provided by the referring officer</p>
                        </div>
                    </div>
                    <div class="bg-white border-2 border-slate-200 rounded-2xl p-6">
                        <p class="text-slate-900 font-semibold leading-relaxed whitespace-pre-wrap">{{ $referral->administrative_observations }}</p>
                    </div>
                </div>

                <!-- Guidance Notes (if status is resolved or notes exist) -->
                @if($referral->guidance_notes || $referral->administrative_status === 'Resolved')
                    <div class="bg-slate-50 border-2 border-slate-200 rounded-3xl p-8">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-10 h-10 bg-green-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-extrabold text-slate-900">Guidance Office Response</h2>
                                <p class="text-sm font-medium text-slate-600 mt-0.5">Follow-up notes from counseling staff</p>
                            </div>
                        </div>
                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-6">
                            @if($referral->guidance_notes)
                                <p class="text-slate-900 font-semibold leading-relaxed whitespace-pre-wrap">{{ $referral->guidance_notes }}</p>
                            @else
                                <p class="text-slate-500 italic">No guidance notes yet</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Status Workflow & Actions -->
                <div class="bg-white border-2 border-slate-200 rounded-3xl p-8">
                    <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Administrative Workflow</h3>

                    <div class="space-y-4">
                        @if($referral->administrative_status === 'Draft')
                            <p class="text-sm text-slate-600 mb-4">This referral is currently in Draft status. You can edit the details or forward it to the guidance office.</p>

                            <a href="{{ route('referrals.edit', $referral->id) }}"
                                class="w-full px-6 py-4 bg-slate-100 text-slate-700 font-bold rounded-2xl hover:bg-slate-200 transition-all flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Referral
                            </a>

                            <form action="{{ route('referrals.update', $referral->id) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="student_id" value="{{ $referral->student_id }}">
                                <input type="hidden" name="case_reference" value="{{ $referral->case_reference }}">
                                <input type="hidden" name="support_urgency" value="{{ $referral->support_urgency }}">
                                <input type="hidden" name="administrative_observations" value="{{ $referral->administrative_observations }}">

                                <!-- Hidden input to update status -->
                                <input type="hidden" name="administrative_status" value="Forwarded to Guidance">

                                <button type="submit"
                                    class="w-full px-6 py-4 bg-[#004d32] text-white font-bold rounded-2xl hover:bg-green-800 transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                    Forward to Guidance Office
                                </button>
                            </form>

                        @elseif($referral->administrative_status === 'Forwarded to Guidance')
                            <p class="text-sm text-slate-600 mb-4">This referral has been forwarded to the guidance office. Update the status once the student has been scheduled.</p>

                            <form action="{{ route('referrals.update', $referral->id) }}" method="POST" class="space-y-3" onsubmit="return confirm('Mark this referral as Scheduled?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="student_id" value="{{ $referral->student_id }}">
                                <input type="hidden" name="case_reference" value="{{ $referral->case_reference }}">
                                <input type="hidden" name="support_urgency" value="{{ $referral->support_urgency }}">
                                <input type="hidden" name="administrative_observations" value="{{ $referral->administrative_observations }}">
                                <input type="hidden" name="administrative_status" value="Scheduled">

                                <button type="submit" class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-2xl hover:bg-green-700 transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Mark as Scheduled
                                </button>
                            </form>

                            <form action="{{ route('referrals.update', $referral->id) }}" method="POST" onsubmit="return confirm('Resolve this referral?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="student_id" value="{{ $referral->student_id }}">
                                <input type="hidden" name="case_reference" value="{{ $referral->case_reference }}">
                                <input type="hidden" name="support_urgency" value="{{ $referral->support_urgency }}">
                                <input type="hidden" name="administrative_observations" value="{{ $referral->administrative_observations }}">
                                <input type="hidden" name="administrative_status" value="Resolved">

                                <button type="submit" class="w-full px-6 py-4 bg-slate-100 text-slate-700 font-bold rounded-2xl hover:bg-slate-200 transition-all flex items-center justify-center gap-3">
                                    Mark as Resolved
                                </button>
                            </form>

                        @elseif($referral->administrative_status === 'Scheduled')
                            <p class="text-sm text-slate-600 mb-4">Student session is scheduled. Mark as resolved once the intervention is complete.</p>

                            <form action="{{ route('referrals.update', $referral->id) }}" method="POST" onsubmit="return confirm('Resolve this referral?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="student_id" value="{{ $referral->student_id }}">
                                <input type="hidden" name="case_reference" value="{{ $referral->case_reference }}">
                                <input type="hidden" name="support_urgency" value="{{ $referral->support_urgency }}">
                                <input type="hidden" name="administrative_observations" value="{{ $referral->administrative_observations }}">
                                <input type="hidden" name="administrative_status" value="Resolved">

                                <button type="submit" class="w-full px-6 py-4 bg-[#004d32] text-white font-bold rounded-2xl hover:bg-green-800 transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Mark as Resolved
                                </button>
                            </form>

                        @else
                            <p class="text-sm text-green-700 font-semibold bg-green-50 border-2 border-green-200 rounded-2xl p-4">
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                This referral is resolved and closed.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Delete Button -->
                <div>
                    <form action="{{ route('referrals.destroy', $referral->id) }}" method="POST" onsubmit="return confirm('Archive this referral?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-4 border-2 border-red-300 text-red-600 font-bold rounded-2xl hover:bg-red-50 transition-all flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Archive Referral
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
