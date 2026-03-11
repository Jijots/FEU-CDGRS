<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">ID Recovery Vault</h1>
                <p class="text-base text-slate-500 font-medium mt-1">Manage pending identifications and view resolution history.</p>
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
                Pending Verification
            </a>
            <a href="{{ route('assets.lost-ids', ['view' => 'history']) }}" class="pb-3 px-2 text-sm font-bold transition-colors {{ $isHistory ? 'text-[#004d32] border-b-4 border-[#004d32]' : 'text-slate-400 hover:text-slate-600 border-b-4 border-transparent' }}">
                Resolved History
            </a>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-200">
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">Tracking No.</th>
                            <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wide">ID Information</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-slate-500 uppercase tracking-wide">System Status</th>
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
                                    @if(isset($item->suggested_owner))
                                        <p class="text-sm font-bold text-[#004d32]">{{ $item->suggested_owner->name }}</p>
                                        <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $item->suggested_owner->id_number }}</p>
                                    @else
                                        <p class="text-sm font-medium text-slate-600 truncate max-w-xs">{{ $item->description }}</p>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @if($isHistory)
                                        <span class="inline-flex px-4 py-1.5 rounded-lg text-xs font-bold uppercase bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                            Successfully Claimed
                                        </span>
                                    @elseif(isset($item->confidence))
                                        <span class="inline-flex px-4 py-1.5 rounded-lg text-xs font-bold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200 shadow-sm">
                                            Smart Match: {{ $item->confidence * 100 }}%
                                        </span>
                                    @else
                                        <span class="inline-flex px-4 py-1.5 rounded-lg text-xs font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                            Awaiting Verification
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right flex items-center justify-end gap-2">
                                    @if(!$isHistory)
                                        <a href="{{ route('assets.show', $item->id) }}" title="Scan & Verify" class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-[#004d32] transition-colors shadow-sm inline-block border-2 border-transparent">
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
                                    <form action="{{ route('assets.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to archive this ID record?');">
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
                                <td colspan="4" class="px-8 py-32 text-center text-slate-400 font-bold text-base">
                                    {{ $isHistory ? 'No claimed IDs found in the history.' : 'The ID Vault is currently empty. No pending IDs found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
