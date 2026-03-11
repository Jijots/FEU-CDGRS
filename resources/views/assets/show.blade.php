<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Registry
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Record #{{ $item->tracking_number }}</h1>
            </div>
            @if($item->status == 'Active')
                <span class="px-4 py-2 bg-green-100 text-green-700 font-bold rounded-xl text-sm border-2 border-green-200 uppercase">Active Record</span>
            @else
                <span class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm border-2 border-slate-200 uppercase">{{ $item->status }}</span>
            @endif
        </div>

        @if(session('info'))
            <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-600 text-blue-800 rounded-r-lg font-bold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-600 text-red-800 rounded-r-lg font-bold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-5 space-y-8">
                <div class="bg-white border-4 border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 p-4 border-b-2 border-slate-200 text-center">
                        <span class="text-sm font-bold text-slate-600 uppercase tracking-wide">
                            {{ $item->report_type }} Asset Capture
                        </span>
                    </div>
                    <div class="p-6 bg-white flex items-center justify-center min-h-[300px]">
                        <img src="{{ $item->image_url }}" alt="Asset Image" class="max-w-full max-h-[350px] object-contain rounded-xl drop-shadow-md">
                    </div>
                </div>

                <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Asset Specifications</h3>
                    <div class="space-y-5">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Item Category</span>
                            <span class="block text-base font-bold text-slate-900">{{ $item->item_category }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Detailed Description</span>
                            <span class="block text-sm font-semibold text-slate-700 leading-relaxed">{{ $item->description }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Location Logged</span>
                            <span class="block text-sm font-bold text-slate-900">{{ $item->location_found }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-sm h-fit">

                    <div class="flex items-center gap-4 border-b-2 border-slate-100 pb-6 mb-8">
                        <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center shadow-inner shrink-0">
                            <svg class="w-6 h-6 text-[#FECB02]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">1-to-Many Discovery Engine</h2>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">Cross-reference this {{ strtolower($item->report_type) }} item against the active database.</p>
                        </div>
                    </div>

                    @if($item->status == 'Active')
                        <div class="border-4 border-slate-100 rounded-2xl p-10 text-center bg-slate-50 mb-6">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-200">
                                <svg class="w-8 h-8 text-[#004d32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">Ready for Database Scan</h3>
                            <p class="text-sm font-medium text-slate-500 mb-8 max-w-md mx-auto leading-relaxed">
                                The Vision AI will map this item's visual SIFT keypoints and automatically search all pending
                                <strong>{{ $item->report_type === 'Found' ? 'Lost' : 'Found' }}</strong> reports in the <strong>{{ $item->item_category }}</strong> category.
                            </p>

                            <a href="{{ route('assets.find-matches', $item->id) }}" class="inline-flex items-center justify-center gap-3 w-full py-5 bg-slate-900 text-white font-bold text-base rounded-xl hover:bg-slate-800 transition-all shadow-md">
                                Run Database Cross-Reference
                                <svg class="w-5 h-5 text-[#FECB02]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </a>
                        </div>
                    @else
                        <div class="border-4 border-green-100 rounded-2xl p-10 text-center bg-green-50 mb-6">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-green-200">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-green-900 mb-2">Ticket Resolved</h3>
                            <p class="text-sm font-medium text-green-700 max-w-md mx-auto">This asset has been successfully matched and claimed. Further AI scans are disabled.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
