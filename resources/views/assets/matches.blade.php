<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b-2 border-slate-100 pb-6">
            <div>
                <a href="{{ route('assets.show', $targetItem->id) }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Asset Record
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">AI Discovery Results</h1>
                <p class="text-base text-slate-500 font-medium mt-1">Cross-referencing Record #{{ $targetItem->tracking_number }} against the active database.</p>
            </div>

            <div class="flex items-center gap-3 bg-slate-50 border-2 border-slate-200 px-5 py-3 rounded-xl shadow-sm">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-slate-200 shrink-0">
                    <svg class="w-6 h-6 text-[#004d32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Engine Status</span>
                    <span class="block text-sm font-bold text-slate-800">Batch Scan Complete</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 mb-10 shadow-sm border-2 border-slate-200 flex items-center gap-6">
            <div class="w-24 h-24 rounded-xl bg-slate-50 border border-slate-200 p-2 shrink-0">
                <img src="{{ $targetItem->image_url }}" alt="Target" class="w-full h-full object-cover rounded-lg">
            </div>
            <div>
                <span class="text-[#004d32] font-bold text-xs uppercase tracking-widest mb-1 block">Target Asset Reference ({{ $targetItem->report_type }})</span>
                <h2 class="text-xl font-bold text-slate-900">{{ $targetItem->item_name }}</h2>
                <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $targetItem->description }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-[#FECB02]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Top Suggested Matches
            </h3>

            @if(count($matches) > 0)
                @foreach($matches as $index => $match)
                    @php
                        $candidate = $match['model'];
                        $score = $match['confidence_score'];

                        if ($score >= 85) {
                            $ringColor = 'ring-green-500';
                            $bgColor = 'bg-green-50';
                            $badgeClass = 'bg-green-100 text-green-800 border-green-200';
                        } elseif ($score >= 50) {
                            $ringColor = 'ring-amber-400';
                            $bgColor = 'bg-amber-50';
                            $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                        } else {
                            $ringColor = 'ring-red-400';
                            $bgColor = 'bg-red-50';
                            $badgeClass = 'bg-red-100 text-red-800 border-red-200';
                        }
                    @endphp

                    @if($candidate)
                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col md:flex-row gap-8 items-start md:items-center">

                            <div class="absolute top-0 left-0 w-2 h-full {{ str_replace('ring-', 'bg-', $ringColor) }}"></div>

                            <div class="w-40 h-40 shrink-0 rounded-xl bg-slate-50 border border-slate-200 p-2 relative">
                                <span class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-[#004d32] text-white font-bold flex items-center justify-center text-sm shadow-md border-2 border-white">
                                    #{{ $index + 1 }}
                                </span>
                                <img src="{{ $candidate->image_url }}" alt="Candidate" class="w-full h-full object-cover rounded-lg">
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-lg border-2 {{ $badgeClass }}">
                                        {{ $score }}% Match
                                    </span>
                                    <span class="text-sm font-bold text-slate-400">Record #{{ $candidate->tracking_number }}</span>
                                </div>
                                <h4 class="text-xl font-extrabold text-slate-900 mb-1">{{ $candidate->item_name }}</h4>
                                <p class="text-sm font-medium text-slate-600 mb-4">{{ $candidate->description }}</p>

                                <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-[#004d32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">System Insight:</span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800 italic leading-relaxed">
                                        "{{ $match['breakdown'] }}"
                                    </p>
                                </div>
                            </div>

                            <div class="w-full md:w-auto shrink-0 flex flex-col gap-3">
                                <form action="{{ route('assets.confirm', $targetItem->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full md:w-auto px-8 py-4 bg-white border-2 border-slate-200 text-slate-800 font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
                                        Resolve & Link Records
                                    </button>
                                </form>
                                <a href="{{ route('assets.show', $candidate->id) }}" target="_blank" class="w-full md:w-auto px-8 py-3 text-center text-sm font-bold text-[#004d32] hover:text-green-800 transition-colors">
                                    View Full Profile
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="bg-white border-2 border-dashed border-slate-300 rounded-3xl p-16 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">No Viable Matches Found</h3>
                    <p class="text-base text-slate-500 max-w-md mx-auto">The Vision Engine scanned the active database but could not confidently match this asset with any pending reports in its category.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
