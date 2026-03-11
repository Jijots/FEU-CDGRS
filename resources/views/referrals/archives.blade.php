<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('referrals.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Active Referrals
            </a>
        </div>

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Archived Referrals</h1>
                <p class="text-slate-600 font-medium mt-1">Soft-deleted records can be restored or permanently removed</p>
            </div>
        </div>

        @if($referrals->isEmpty())
            <div class="bg-white border-2 border-slate-200 rounded-3xl p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">No Archived Referrals</h3>
                <p class="text-slate-600">No soft-deleted referrals currently exist in the system.</p>
            </div>
        @else
            <div class="bg-white border-2 border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-200 bg-slate-50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">ID #</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Archived Date</th>
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
                                        <span class="text-slate-700">{{ $referral->deleted_at->format('M d, Y · H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-slate-200 text-slate-600 font-semibold rounded-lg text-sm">{{ $referral->administrative_status }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <form action="{{ route('referrals.restore', $referral->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-bold text-[#004d32] hover:bg-slate-100 rounded-lg transition-colors"
                                                onclick="return confirm('Restore this referral?')">
                                                Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('referrals.forceDelete', $referral->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                onclick="return confirm('Permanently delete this referral? This action cannot be undone.')">
                                                Delete
                                            </button>
                                        </form>
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
