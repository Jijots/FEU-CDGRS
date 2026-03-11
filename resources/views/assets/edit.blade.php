<x-app-layout>
    <div class="max-w-4xl mx-auto px-8 lg:px-12 py-10">

        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b-2 border-slate-100 pb-6">
            <div>
                <a href="{{ $item->item_category === 'ID / Identification' ? route('assets.lost-ids') : route('assets.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Return to {{ $item->item_category === 'ID / Identification' ? 'ID Recovery Vault' : 'Active Registry' }}
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Modify Record #{{ $item->tracking_number }}</h1>
            </div>
            <span class="px-5 py-2.5 bg-amber-50 text-amber-700 font-bold rounded-2xl text-xs border-2 border-amber-200 uppercase tracking-widest shadow-sm">Registry Edit Mode</span>
        </div>

        <form action="{{ route('assets.update', $item->id) }}" method="POST" enctype="multipart/form-data" x-data="{ photoPreview: '{{ asset('storage/' . $item->image_path) }}' }" class="space-y-8 bg-white border-2 border-slate-200 rounded-3xl p-10 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Report Classification</label>
                    <select name="report_type" class="w-full bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 text-sm font-bold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-all">
                        <option value="Lost" {{ $item->report_type == 'Lost' ? 'selected' : '' }}>Missing Item Log</option>
                        <option value="Found" {{ $item->report_type == 'Found' ? 'selected' : '' }}>Found Item Log</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Asset Category</label>
                    <select name="item_category" class="w-full bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 text-sm font-bold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-all">
                        <option {{ $item->item_category == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                        <option {{ $item->item_category == 'ID / Identification' ? 'selected' : '' }}>ID / Identification</option>
                        <option {{ $item->item_category == 'Bags / Backpacks' ? 'selected' : '' }}>Bags / Backpacks</option>
                        <option {{ $item->item_category == 'Clothing / Apparel' ? 'selected' : '' }}>Clothing / Apparel</option>
                        <option {{ $item->item_category == 'Books / Documents' ? 'selected' : '' }}>Books / Documents</option>
                        <option {{ $item->item_category == 'Keys / Accessories' ? 'selected' : '' }}>Keys / Accessories</option>
                        <option {{ $item->item_category == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Item Title</label>
                    <input type="text" name="item_name" value="{{ $item->item_name }}" class="w-full bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 text-sm font-bold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-all" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Surrender/Loss Location</label>
                    <input type="text" name="location_found" value="{{ $item->location_found ?? $item->location_lost }}" class="w-full bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 text-sm font-bold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-all" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Comprehensive Description</label>
                <textarea name="description" rows="4" class="w-full bg-slate-50 border-2 border-slate-200 rounded-2xl p-5 text-sm font-medium text-slate-700 focus:border-[#004d32] focus:ring-0 resize-none leading-relaxed transition-all" required>{{ $item->description }}</textarea>
            </div>

            <div class="border-t-2 border-slate-100 pt-8">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 ml-1">Visual Evidence</label>
                <div class="border-4 border-dashed border-slate-200 rounded-3xl p-10 text-center hover:border-[#004d32] transition-all group bg-slate-50 cursor-pointer mb-6" @click="$refs.photo.click()">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="h-48 mx-auto rounded-2xl object-contain mb-6 border-4 border-white shadow-md bg-white p-2">
                    </template>
                    <input type="file" name="image" class="hidden" x-ref="photo" @change="const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL($refs.photo.files[0]);">
                    <span class="block text-sm font-bold text-slate-600 group-hover:text-[#004d32] transition-colors">Select to replace existing record image</span>
                </div>

                <div class="flex items-start gap-4 p-6 bg-blue-50 border-2 border-blue-200 rounded-2xl cursor-pointer hover:border-blue-300 transition-all shadow-sm" onclick="document.getElementById('is_stock_image').click();">
                    <div class="flex items-center h-6 shrink-0 mt-0.5">
                        <input type="checkbox" name="is_stock_image" id="is_stock_image" value="1" onclick="event.stopPropagation()" {{ $item->is_stock_image ? 'checked' : '' }} class="w-6 h-6 text-[#004d32] border-2 border-blue-300 rounded focus:ring-[#004d32] transition-all cursor-pointer bg-white">
                    </div>
                    <div>
                        <label for="is_stock_image" class="block text-sm font-extrabold text-blue-900 cursor-pointer">Classify as Visual Reference (Stock Image)</label>
                        <p class="text-xs font-medium text-blue-700 mt-1 leading-relaxed">Check this box if the photo is a general reference and not a capture of the specific surrendered item.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t-2 border-slate-100">
                <a href="{{ $item->item_category === 'ID / Identification' ? route('assets.lost-ids') : route('assets.index') }}" class="w-full sm:w-auto px-10 py-5 text-center text-sm font-bold text-slate-600 bg-white border-2 border-slate-200 rounded-2xl hover:bg-slate-50 transition-all shadow-sm">Discard Corrections</a>
                <button type="submit" class="w-full py-5 bg-[#004d32] text-white text-sm font-extrabold rounded-2xl hover:bg-green-800 transition-all shadow-md text-center flex-1 border-2 border-transparent">
                    Finalize Record Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
