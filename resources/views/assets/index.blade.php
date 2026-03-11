<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Asset Management</h1>
                <p class="text-base text-slate-500 font-medium mt-1">Manage, search, and verify all reported items.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('assets.archived') }}" class="px-5 py-3 bg-slate-100 border-2 border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors shadow-sm text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Archives
                </a>
                <a href="{{ route('assets.create') }}" class="px-6 py-3 bg-[#004d32] text-white font-bold rounded-xl hover:bg-green-800 transition-colors shadow-sm text-sm border-2 border-transparent">
                    Log New Item
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-800 rounded-r-lg font-bold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-4 border-2 border-slate-200 rounded-2xl flex flex-col md:flex-row justify-between gap-4 mb-6 shadow-sm">
            <div class="flex gap-2">
                <a href="{{ route('assets.index', ['type' => 'Lost', 'view' => request('view')]) }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ request('type', 'Lost') === 'Lost' ? 'bg-[#FECB02] text-[#004d32] border-2 border-[#FECB02]' : 'text-slate-500 hover:bg-slate-50 border-2 border-transparent' }}">
                    Missing Items
                </a>
                <a href="{{ route('assets.index', ['type' => 'Found', 'view' => request('view')]) }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ request('type', 'Lost') === 'Found' ? 'bg-[#FECB02] text-[#004d32] border-2 border-[#FECB02]' : 'text-slate-500 hover:bg-slate-50 border-2 border-transparent' }}">
                    Found Items
                </a>
            </div>

            <form method="GET" action="{{ route('assets.index') }}" class="relative w-full md:w-96">
                <input type="hidden" name="type" value="{{ request('type', 'Lost') }}">
                <input type="hidden" name="view" value="{{ request('view') }}">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tracking number or item..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-[#004d32] focus:ring-0 text-sm font-semibold transition-colors placeholder:font-medium">
            </form>
        </div>

        <div class="mb-6 flex space-x-6 border-b-2 border-slate-200">
            <a href="{{ route('assets.index', ['type' => request('type', 'Lost')]) }}" class="pb-3 px-2 text-sm font-bold transition-colors {{ !$isHistory ? 'text-[#004d32] border-b-4 border-[#004d32]' : 'text-slate-400 hover:text-slate-600 border-b-4 border-transparent' }}">
                Active Inventory
            </a>
            <a href="{{ route('assets.index', ['type' => request('type', 'Lost'), 'view' => 'history']) }}" class="pb-3 px-2 text-sm font-bold transition-colors {{ $isHistory ? 'text-[#004d32] border-b-4 border-[#004d32]' : 'text-slate-400 hover:text-slate-600 border-b-4 border-transparent' }}">
                Resolved History
            </a>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-200">
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">Tracking No.</th>
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">Item & Category</th>
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">Details</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="px-8 py-5 text-right text-sm font-bold text-slate-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100">
                        @forelse ($items as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-slate-800 tracking-wider">#{{ $item->tracking_number }}</span>
                                </td>

                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden border-2 border-slate-200 bg-white shrink-0 shadow-sm flex items-center justify-center">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="Item" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $item->item_name ?? 'Unnamed Item' }}</p>
                                            <span class="inline-flex mt-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">{{ $item->item_category }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-slate-800">{{ $item->location_found ?? $item->location_lost }}</p>
                                    <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $item->date_found ? \Carbon\Carbon::parse($item->date_found)->format('M d, Y') : \Carbon\Carbon::parse($item->date_lost)->format('M d, Y') }}</p>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    @if($isHistory)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase bg-green-100 text-green-700 border border-green-200">
                                            Successfully Claimed
                                        </span>
                                    @elseif($item->status == 'Active')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase bg-blue-100 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-lg text-xs font-bold uppercase bg-slate-100 text-slate-500 border border-slate-200">{{ $item->status }}</span>
                                    @endif
                                </td>

                                <td class="px-8 py-5 text-right flex items-center justify-end gap-2">
                                    @if(!$isHistory)
                                        <a href="{{ route('assets.show', $item->id) }}" title="Verify Record" class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-[#004d32] transition-colors shadow-sm inline-block border-2 border-transparent">
                                            Verify
                                        </a>
                                    @else
                                        <a href="{{ route('assets.show', $item->id) }}" title="View Record" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition-colors shadow-sm inline-block border-2 border-slate-200">
                                            View Details
                                        </a>
                                    @endif

                                    <a href="{{ route('assets.edit', $item->id) }}" title="Edit Record" class="px-4 py-2 text-slate-600 bg-slate-100 text-sm font-bold rounded-lg hover:bg-amber-100 hover:text-amber-800 transition-colors shadow-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('assets.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to archive this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Archive Record" class="px-4 py-2 text-slate-600 bg-slate-100 text-sm font-bold rounded-lg hover:bg-red-100 hover:text-red-800 transition-colors shadow-sm">
                                            Archive
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-32 text-center text-slate-400 font-bold text-base">
                                    {{ $isHistory ? 'No resolved items found in the history.' : 'No active records found in inventory.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
