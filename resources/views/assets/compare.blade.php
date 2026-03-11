<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 lg:px-12 py-12">

        <div class="flex flex-col items-center mb-12 text-center">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Identity & Asset Verification</h1>

            <div class="inline-flex items-center gap-3 mt-4 px-6 py-2.5 rounded-full border-2 {{ $isMatch ? 'bg-green-50 border-green-200 text-[#004d32]' : 'bg-red-50 border-red-200 text-red-700' }}">
                <span class="w-2.5 h-2.5 rounded-full {{ $isMatch ? 'bg-green-500' : 'bg-red-600' }} animate-pulse"></span>
                <span class="text-sm font-bold uppercase tracking-wide">
                    {{ $isMatch ? 'Registry Verification Active' : 'System Alert: Physical Mismatch Detected' }}
                </span>
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-3xl p-8 lg:p-12 shadow-sm mb-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-12">

                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-4">
                        <span class="text-sm font-bold text-slate-500 uppercase tracking-wide">Official Registry Record</span>
                        <span class="text-sm font-bold text-slate-900">{{ $targetItem->item_name ?? $targetItem->item_category }}</span>
                    </div>

                    <div class="h-[400px] bg-slate-50 border-4 border-slate-200 rounded-2xl p-6 flex items-center justify-center overflow-hidden shadow-inner">
                        <img src="{{ $targetItem->image_url }}" onerror="this.src='{{ asset('images/placeholder.png') }}'" class="max-w-full h-full object-contain drop-shadow-md">
                    </div>
                </div>

                <div class="space-y-4 relative">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-4">
                        <span class="text-sm font-bold text-[#004d32] uppercase tracking-wide">Live Evidence Verification</span>
                    </div>

                    @php $themeColor = $isMatch ? 'border-[#004d32]' : 'border-red-500'; @endphp
                    @php $badgeColor = $isMatch ? 'bg-[#004d32]' : 'bg-red-800'; @endphp

                    <div class="h-[400px] bg-slate-50 border-4 {{ $themeColor }} rounded-2xl p-6 flex items-center justify-center relative shadow-inner">
                        <img src="{{ asset($comparisonImagePath) }}" class="max-w-full h-full object-contain drop-shadow-md">

                        <div class="absolute -top-8 -right-4 lg:-right-10 w-72 {{ $badgeColor }} text-white p-6 rounded-2xl shadow-2xl border-4 border-white z-10 transition-all">
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <span class="block text-xs font-bold text-white/70 uppercase tracking-wide mb-1">Confidence Score</span>
                                    <span class="text-4xl font-extrabold">{{ $similarityScore }}%</span>
                                </div>
                                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $isMatch ? 'M5 13l4 4L19 7' : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' }}"></path></svg>
                                </div>
                            </div>

                            <details class="group border-t-2 border-white/10 pt-4 cursor-pointer" open>
                                <summary class="list-none flex items-center justify-between text-xs font-bold uppercase tracking-wide text-white/80 hover:text-white transition-colors">
                                    Data Consistency
                                    <svg class="w-4 h-4 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </summary>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-[10px] font-bold opacity-70 uppercase tracking-wide">Visual Characteristic Mapping</span>
                                            <span class="text-xs font-bold">{{ $visualScore }}%</span>
                                        </div>
                                        <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                                            <div class="bg-white h-full transition-all duration-1000" style="width: {{ $visualScore }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 p-8 bg-slate-50 border-2 border-slate-200 rounded-3xl shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <svg class="w-6 h-6 text-[#004d32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Registry Intelligence Insight:</span>
                </div>
                <p class="text-lg font-bold text-slate-800 italic leading-relaxed">
                    "{{ $breakdown }}"
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-6">
            <a href="{{ route('assets.index') }}" class="px-10 py-5 bg-white border-2 border-slate-200 text-slate-600 font-bold text-base rounded-2xl hover:bg-slate-50 transition-all text-center shadow-sm">
                Discard Comparison
            </a>

            @if ($isMatch)
                <form action="{{ route('assets.confirm', $targetItem->id) }}" method="POST" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit" class="w-full px-12 py-5 bg-[#004d32] text-white font-bold text-base rounded-2xl hover:bg-green-800 transition-all shadow-md border-2 border-transparent flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Confirm Match & Finalize Record
                    </button>
                </form>
            @endif
        </div>

    </div>
</x-app-layout>
