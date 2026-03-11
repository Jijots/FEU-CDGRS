<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Supportive Intervention Hub</h1>
                <p class="text-slate-500 font-medium mt-1">Manage counseling referrals and student support interventions</p>
            </div>
            <a href="{{ route('referrals.create') }}"
                class="inline-flex items-center gap-2 px-8 py-4 bg-[#004d32] text-white font-bold rounded-xl hover:bg-green-800 transition-all shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                New Referral
            </a>
        </div>

        <!-- Status Filter -->
        <div class="mb-8 flex flex-wrap gap-3">
            <a href="{{ route('referrals.index') }}"
                class="px-6 py-3 rounded-xl font-bold transition-all {{ !request('status') ? 'bg-[#004d32] text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All Referrals ({{ $referrals->count() }})
            </a>
            @foreach($statuses as $status)
                <a href="{{ route('referrals.index', ['status' => $status['value']]) }}"
                    class="px-6 py-3 rounded-xl font-bold transition-all {{ request('status') === $status['value'] ? 'bg-[#004d32] text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $status['label'] }}
                </a>
            @endforeach
        </div>

        @if($referrals->isEmpty())
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">No Referrals Currently Pending</h3>
                <p class="text-slate-600 mb-6">Create a new supportive intervention referral to get started.</p>
                <a href="{{ route('referrals.create') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-[#004d32] text-white font-bold rounded-xl hover:bg-green-800 transition-all">
                    Create First Referral
                </a>
            </div>
        @else
            <div class="bg-white border-2 border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-200 bg-slate-50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">ID #</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Case Reference</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Urgency</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($referrals as $referral)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $referral->student->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $referral->student->program_code ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-slate-700">{{ $referral->student->id_number }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-slate-700">{{ $referral->case_reference ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($referral->support_urgency === 'Standard')
                                            <span class="px-3 py-1 bg-slate-200 text-slate-600 font-semibold rounded-lg text-sm">Standard</span>
                                        @elseif($referral->support_urgency === 'Priority')
                                            <span class="px-3 py-1 bg-amber-200 text-amber-700 font-semibold rounded-lg text-sm">Priority</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-200 text-red-700 font-semibold rounded-lg text-sm">Immediate</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($referral->administrative_status === 'Draft')
                                            <span class="px-3 py-1 bg-slate-100 text-slate-600 font-semibold rounded-lg text-sm">Draft</span>
                                        @elseif($referral->administrative_status === 'Forwarded to Guidance')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 font-semibold rounded-lg text-sm">Forwarded</span>
                                        @elseif($referral->administrative_status === 'Scheduled')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 font-semibold rounded-lg text-sm">Scheduled</span>
                                        @else
                                            <span class="px-3 py-1 bg-slate-200 text-slate-600 font-semibold rounded-lg text-sm">Resolved</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('referrals.show', $referral->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-bold text-[#004d32] hover:bg-slate-100 rounded-lg transition-colors">
                                            View
                                        </a>
                                        @if($referral->administrative_status === 'Draft')
                                            <a href="{{ route('referrals.edit', $referral->id) }}"
                                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-bold text-[#004d32] hover:bg-slate-100 rounded-lg transition-colors">
                                                Edit
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
