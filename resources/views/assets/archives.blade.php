<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b-2 border-slate-200 pb-5">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ request('category') === 'ID' ? route('assets.lost-ids') : route('assets.index') }}" title="Return to Active Registry" class="text-slate-400 hover:text-[#004d32] transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        {{ request('category') === 'ID' ? 'Archived ID Recovery Records' : 'Registry Archives' }}
                    </h1>
                </div>
                <p class="text-sm text-slate-500 mt-1 font-bold uppercase tracking-widest ml-9">Historical & Soft-Deleted Data Log</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-800 rounded-r-lg font-bold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border-2 border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b-2 border-slate-200 px-8 py-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs font-bold text-slate-600 uppercase tracking-widest leading-relaxed">
                        Data Retention Policy: Permanently deleting records here will erase all physical image evidence and metadata from the university server.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-500 font-extrabold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-8 py-5 border-b border-slate-200">Date Archived</th>
                            <th class="px-8 py-5 border-b border-slate-200">Asset Title</th>
                            <th class="px-8 py-5 border-b border-slate-200">Category</th>
                            <th class="px-8 py-5 border-b border-slate-200">Tracking Reference</th>
                            <th class="px-8 py-5 border-b border-slate-200 text-right">Administrative Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-8 py-5 font-bold text-slate-600">
                                {{ \Carbon\Carbon::parse($item->deleted_at)->format('M d, Y') }}
                                <span class="block text-[10px] text-slate-400 uppercase tracking-tight">{{ \Carbon\Carbon::parse($item->deleted_at)->format('h:i A') }}</span>
                            </td>

                            <td class="px-8 py-5">
                                <p class="font-extrabold text-slate-800 text-base leading-tight">{{ $item->item_name }}</p>
                                <p class="text-xs font-bold text-[#004d32] mt-0.5 uppercase tracking-wide">{{ $item->report_type }}</p>
                            </td>

                            <td class="px-8 py-5 font-bold text-slate-500">
                                {{ $item->item_category }}
                            </td>

                            <td class="px-8 py-5 font-mono text-xs font-bold text-slate-400">
                                #{{ $item->tracking_number }}
                            </td>

                            <td class="px-8 py-5 text-right space-x-2">
                                <form action="{{ route('assets.restore', $item->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 text-xs font-bold text-green-700 hover:text-green-900 transition-all px-4 py-2 bg-green-50 rounded-xl border-2 border-green-100 hover:border-green-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Restore Record
                                    </button>
                                </form>

                                <form action="{{ route('assets.force-delete', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirm Permanent Erasure: This will remove all image evidence and registry data. Continue?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 text-xs font-bold text-red-700 hover:text-red-900 transition-all px-4 py-2 bg-red-50 rounded-xl border-2 border-red-100 hover:border-red-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Erase Permanently
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-slate-100">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-500 uppercase tracking-widest">Archive Vault Empty</h3>
                                <p class="text-sm font-medium text-slate-400 mt-2">No historical records are currently held in this section.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
