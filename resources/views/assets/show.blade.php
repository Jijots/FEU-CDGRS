<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 h18"></path></svg>
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <div class="lg:col-span-5 space-y-8">
                <div class="bg-white border-4 border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 p-4 border-b-2 border-slate-200 text-center">
                        <span class="text-sm font-bold text-slate-600 uppercase tracking-wide">Original Item Photo</span>
                    </div>
                    <div class="p-6 bg-white flex items-center justify-center min-h-[300px]">
                        <img src="{{ $item->image_url }}" alt="Item" class="max-w-full max-h-[350px] object-contain rounded-xl drop-shadow-md">
                    </div>
                </div>

                <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-4 mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Asset Details</h3>
                        <a href="{{ route('assets.edit', $item->id) }}" class="p-2 text-slate-400 hover:text-[#004d32] border-2 border-transparent hover:border-slate-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                    </div>

                    <div class="space-y-5">
                        <div class="mb-5 pb-5 border-b-2 border-slate-100">
                            <span class="block text-sm font-bold text-slate-500 uppercase tracking-wide mb-1">Item Details</span>
                            <p class="text-xl font-extrabold text-slate-900">{{ $item->item_name ?? 'Unnamed Item' }}</p>
                            <span class="inline-flex mt-2 px-3 py-1 rounded-lg text-xs font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">{{ $item->item_category }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-500 uppercase tracking-wide mb-1">Location Logged</span>
                            <p class="text-base font-semibold text-slate-900">{{ $item->location_found ?? $item->location_lost }}</p>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-500 uppercase tracking-wide mb-1">Date Logged</span>
                            <p class="text-base font-semibold text-slate-900">{{ $item->date_found ? \Carbon\Carbon::parse($item->date_found)->format('F d, Y') : \Carbon\Carbon::parse($item->date_lost)->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-slate-500 uppercase tracking-wide mb-1">Description</span>
                            <p class="text-base font-medium text-slate-700 bg-slate-50 p-4 rounded-xl border-2 border-slate-100 mt-2">{{ $item->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-sm h-fit">

                    <div class="flex items-center gap-4 border-b-2 border-slate-100 pb-6 mb-8">
                        <div class="w-12 h-12 bg-[#004d32] rounded-xl flex items-center justify-center shadow-inner shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Smart Visual Scanner</h2>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">Upload a photo of the recovered item to verify a match.</p>
                        </div>
                    </div>

                    <form action="{{ route('assets.compare', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="border-4 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-[#004d32] hover:bg-slate-50 transition-all cursor-pointer bg-slate-50 mb-6" onclick="document.getElementById('comparison_image').click()" id="uploadWrapper">
                            <svg class="w-12 h-12 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="block text-sm font-bold text-slate-600">Click to upload live capture</span>
                            <span class="block text-xs font-medium text-slate-400 mt-1">PNG, JPG up to 10MB</span>
                            <input type="file" id="comparison_image" accept="image/*" class="hidden">
                        </div>

                        <div id="cropperWrapper" class="hidden mb-6">
                            <div class="h-[400px] bg-slate-900 rounded-2xl overflow-hidden border-4 border-slate-200 mb-4">
                                <img id="cropperImage" class="max-w-full">
                            </div>
                            <button type="button" id="cropButton" class="w-full py-4 bg-slate-800 text-white font-bold text-base rounded-xl hover:bg-slate-700 transition-colors shadow-sm">
                                Confirm Crop & Prepare Image
                            </button>
                        </div>

                        <div id="previewWrapper" class="hidden mb-6 text-center">
                            <span class="block text-sm font-bold text-green-600 mb-4">Image Ready for Processing</span>
                            <div class="border-4 border-slate-200 rounded-2xl p-4 bg-white inline-block">
                                <img id="croppedPreview" class="h-48 object-contain rounded-lg">
                            </div>
                        </div>

                        <input type="hidden" name="cropped_image" id="cropped_image">

                        <div class="mb-6 text-left">
                            <label for="comparison_notes" class="block text-sm font-bold text-slate-700 mb-2">Item Specifics (Optional but Recommended)</label>
                            <input type="text" name="comparison_notes" id="comparison_notes" placeholder="e.g., 32oz, has a dent on the bottom, dark blue..." class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl focus:border-[#004d32] focus:ring-0 text-sm font-medium transition-colors placeholder:text-slate-400">
                        </div>
                        <button type="submit" id="submitBtn" disabled class="w-full py-5 bg-[#004d32] text-white font-bold text-base rounded-xl opacity-50 cursor-not-allowed flex items-center justify-center gap-3 transition-all border-2 border-transparent">
                            Run Smart Verification Match
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper = null;
            const fileInput = document.getElementById('comparison_image');
            const cropperImage = document.getElementById('cropperImage');
            const cropperWrapper = document.getElementById('cropperWrapper');
            const uploadWrapper = document.getElementById('uploadWrapper');
            const cropButton = document.getElementById('cropButton');
            const previewWrapper = document.getElementById('previewWrapper');
            const croppedPreview = document.getElementById('croppedPreview');
            const hiddenInput = document.getElementById('cropped_image');
            const submitBtn = document.getElementById('submitBtn');

            if(fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    uploadWrapper.style.display = 'none';
                    cropperWrapper.style.display = 'block';
                    previewWrapper.style.display = 'none';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.remove('hover:bg-green-800', 'shadow-md');

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        cropperImage.src = event.target.result;
                        if (cropper) { cropper.destroy(); }
                        cropper = new Cropper(cropperImage, {
                            viewMode: 1, responsive: true,
                            guides: true, center: true, highlight: false,
                            background: false, cropBoxMovable: true, cropBoxResizable: true,
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            if(cropButton) {
                cropButton.addEventListener('click', function() {
                    if (!cropper) return;
                    const canvas = cropper.getCroppedCanvas({
                        maxWidth: 1024, maxHeight: 1024,
                        imageSmoothingEnabled: true, imageSmoothingQuality: 'high',
                    });

                    const base64 = canvas.toDataURL('image/jpeg', 0.95);
                    hiddenInput.value = base64;
                    croppedPreview.src = base64;

                    cropperWrapper.style.display = 'none';
                    previewWrapper.style.display = 'block';

                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.classList.add('hover:bg-green-800', 'shadow-md');
                    }
                });
            }
        });
    </script>
</x-app-layout>
