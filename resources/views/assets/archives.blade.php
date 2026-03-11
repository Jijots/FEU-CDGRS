<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b-2 border-slate-300 pb-5">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ request('category') === 'ID' ? route('assets.lost-ids') : route('assets.index') }}" title="Back to {{ request('category') === 'ID' ? 'ID Vault' : 'Registry' }}" class="text-slate-400 hover:text-[#004d32] transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <h1 class="text-3xl font-extrabold text-slate-500 tracking-tight flex items-center gap-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        {{ request('category') === 'ID' ? 'Archived ID Cards' : 'Archived Assets' }}
                    </h1>
                </div>
                <p class="text-sm text-slate-400 mt-1 font-bold uppercase tracking-widest ml-9">Deleted & Hidden Records</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-800 rounded-r-lg font-bold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden opacity-90">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-3">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Warning: Force Deleting records here will permanently erase them from the database and delete associated images.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-100 text-slate-500 font-extrabold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-5 border-b border-slate-200">Date Deleted</th>
                            <th class="p-5 border-b border-slate-200">Item Name</th>
                            <th class="p-5 border-b border-slate-200">Category</th>
                            <th class="p-5 border-b border-slate-200">Tracking No.</th>
                            <th class="p-5 border-b border-slate-200 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-5 font-semibold text-red-600">{{ \Carbon\Carbon::parse($item->deleted_at)->format('M d, Y - h:i A') }}</td>

                            <td class="p-5">
                                <p class="font-extrabold text-slate-600 text-base">{{ $item->item_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $item->report_type }}</p>
                            </td>

                            <td class="p-5 font-bold text-slate-500">
                                {{ $item->item_category }}
                            </td>

                            <td class="p-5 font-mono text-xs font-bold text-slate-400">
                                {{ $item->tracking_number }}
                            </td>

                            <td class="p-5 text-right space-x-3">
                                <form action="{{ route('assets.restore', $item->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 text-sm font-bold text-green-600 hover:text-green-800 transition-colors px-3 py-1.5 bg-green-50 rounded-lg border border-transparent hover:border-green-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Restore
                                    </button>
                                </form>

                                <form action="{{ route('assets.force-delete', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('CRITICAL WARNING: Are you sure you want to permanently delete this asset? This action cannot be undone and will erase the photo from the server.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-sm font-bold text-red-600 hover:text-red-800 transition-colors px-3 py-1.5 bg-red-50 rounded-lg border border-transparent hover:border-red-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Wipe
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                </div>
                                <h3 class="text-sm font-extrabold text-slate-500 uppercase tracking-wide">Archive Empty</h3>
                                <p class="text-sm text-slate-400 mt-1">No hidden records found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
