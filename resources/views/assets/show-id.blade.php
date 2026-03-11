<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a href="{{ route('assets.lost-ids') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Recovery Vault
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Identification Record #{{ $asset->tracking_number }}</h1>
            </div>
            @if ($asset->status == 'Active')
                <span class="px-4 py-2 bg-green-100 text-[#004d32] font-bold rounded-xl text-sm border-2 border-green-200 uppercase tracking-wide">Active Vault Record</span>
            @else
                <span class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm border-2 border-slate-200 uppercase tracking-wide">{{ $asset->status }}</span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-5 space-y-8">
                <div class="bg-white border-4 border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 p-4 border-b-2 border-slate-200 text-center">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Official Document Capture</span>
                    </div>
                    <div class="p-6 bg-white flex items-center justify-center min-h-[300px]">
                        <img src="{{ $asset->image_url }}" alt="ID Image" class="max-w-full h-full object-contain rounded-2xl drop-shadow-md">
                    </div>
                </div>

                <div class="bg-white border-2 border-slate-200 rounded-3xl p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Surrender Logistics</h3>
                    <div class="space-y-6">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Extracted Information</span>
                            <span class="block text-sm font-bold text-slate-900 leading-relaxed">{{ $asset->description }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Turn-over Location</span>
                            <span class="block text-sm font-bold text-slate-900">{{ $asset->location_found }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white border-2 border-slate-200 rounded-3xl p-8 shadow-sm h-fit">
                    <div class="flex items-center gap-4 border-b-2 border-slate-100 pb-6 mb-8">
                        <div class="w-12 h-12 bg-[#004d32] rounded-2xl flex items-center justify-center shadow-inner shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Registry Identity Verification</h2>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">Automated cross-reference against the FEU Student Information System.</p>
                        </div>
                    </div>

                    @if ($asset->status == 'Active')
                        <div class="bg-slate-50 border-2 border-slate-200 rounded-2xl p-10 text-center">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border-2 border-slate-100">
                                <svg class="w-8 h-8 text-[#004d32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>

                            <h3 class="text-lg font-bold text-slate-800 mb-2">Deploy Registry Intelligence</h3>
                            <p class="text-sm font-medium text-slate-500 mb-8 max-w-md mx-auto leading-relaxed">
                                The system will analyze the identification card and attempt to resolve the student's profile for official notification.
                            </p>

                            <form action="{{ route('assets.compare', $asset->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="manual_id" value="{{ preg_match('/\d{9}/', $asset->description, $ms) ? $ms[0] : '' }}">

                                <button type="submit" class="w-full py-5 bg-[#004d32] text-white font-bold text-lg rounded-2xl hover:bg-green-800 transition-all shadow-md flex items-center justify-center gap-3">
                                    Search Student Registry
                                    <svg class="w-5 h-5 text-[#FECB02]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-2 border-green-200 rounded-2xl p-10 text-center bg-green-50/50">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-green-200">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-green-900 mb-2">Collection Complete</h3>
                            <p class="text-sm font-medium text-green-700">This identity card has been successfully returned to the owner.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
