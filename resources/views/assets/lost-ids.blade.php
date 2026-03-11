<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Official ID Recovery Vault</h1>
                <p class="text-base text-slate-500 font-medium mt-1">Registry of surrendered identifications awaiting owner verification and collection.</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="{{ route('assets.archived', ['category' => 'ID']) }}" class="flex-1 md:flex-none px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors shadow-sm text-sm border-2 border-slate-200 text-center">
                    View Archives
                </a>
                <a href="{{ route('assets.create-id') }}" class="flex-1 md:flex-none px-6 py-3 bg-[#004d32] text-white font-bold rounded-xl hover:bg-green-800 transition-colors shadow-sm text-sm border-2 border-transparent text-center">
                    Log Found ID Card
                </a>
            </div>
        </div>

        <div class="mb-6 flex space-x-6 border-b-2 border-slate-200">
            <a href="{{ route('assets.lost-ids') }}" class="pb-3 px-2 text-sm font-bold transition-colors {{ !$isHistory ? 'text-[#004d32] border-b-4 border-[#004d32]' : 'text-slate-400 hover:text-slate-600 border-b-4 border-transparent' }}">
                Pending Collection
            </a>
            <a href="{{ route('assets.lost-ids', ['view' => 'history']) }}" class="pb-3 px-2 text-sm font-bold transition-colors {{ $isHistory ? 'text-[#004d32] border-b-4 border-[#004d32]' : 'text-slate-400 hover:text-slate-600 border-b-4 border-transparent' }}">
                Resolution History
            </a>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-200">
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">Tracking No.</th>
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">Identified Student</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-slate-500 uppercase tracking-wide">Verification Status</th>
                            <th class="px-8 py-5 text-right text-sm font-bold text-slate-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100">
                        @forelse ($ids as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-slate-800">#{{ $item->tracking_number }}</span>
                                </td>

                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <div>
                                            @if(isset($item->suggested_owner))
                                                <p class="text-sm font-bold text-[#004d32]">{{ $item->suggested_owner->name }}</p>
                                                <p class="text-xs font-semibold text-slate-500 mt-0.5">Student ID: {{ $item->suggested_owner->id_number }}</p>
                                            @else
                                                <p class="text-sm font-bold text-slate-700">Unidentified Record</p>
                                                <p class="text-xs font-medium text-slate-400 mt-0.5 truncate max-w-xs">{{ $item->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    @if($isHistory)
                                        <span class="inline-flex px-4 py-1.5 rounded-lg text-xs font-bold uppercase bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                            Claimed by Owner
                                        </span>
                                    @elseif(isset($item->suggested_owner))
                                        <span class="inline-flex px-4 py-1.5 rounded-lg text-xs font-bold uppercase bg-[#FECB02] text-[#004d32] border border-[#FECB02] shadow-sm">
                                            Registry Match Found
                                        </span>
                                    @else
                                        <span class="inline-flex px-4 py-1.5 rounded-lg text-xs font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                            Awaiting Identity Scan
                                        </span>
                                    @endif
                                </td>

                                <td class="px-8 py-5 text-right flex items-center justify-end gap-2">
                                    @if(!$isHistory)
                                        <a href="{{ route('assets.show', $item->id) }}" title="Review Identity" class="px-4 py-2 bg-slate-900 text-white text-sm font-bold rounded-lg hover:bg-[#004d32] transition-colors shadow-sm inline-block border-2 border-transparent">
                                            Review
                                        </a>
                                    @else
                                        <a href="{{ route('assets.show', $item->id) }}" title="View Resolution" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition-colors shadow-sm inline-block border-2 border-slate-200">
                                            View
                                        </a>
                                    @endif

                                    <a href="{{ route('assets.edit', $item->id) }}" title="Modify Record" class="px-4 py-2 text-slate-600 bg-slate-100 text-sm font-bold rounded-lg hover:bg-amber-100 hover:text-amber-800 transition-colors shadow-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('assets.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Archive this ID record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Archive ID" class="px-4 py-2 text-slate-600 bg-slate-100 text-sm font-bold rounded-lg hover:bg-red-100 hover:text-red-800 transition-colors shadow-sm">
                                            Archive
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-32 text-center text-slate-400 font-bold text-base">
                                    {{ $isHistory ? 'No collection history found.' : 'The recovery vault is currently empty.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
