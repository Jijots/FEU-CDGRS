<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <div class="max-w-7xl mx-auto px-8 py-10">

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a href="{{ route('assets.lost-ids') }}"
                    class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#004d32] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M15 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to ID Vault
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">ID Record
                    #{{ $asset->tracking_number }}</h1>
            </div>
            @if ($asset->status == 'Active')
                <span
                    class="px-4 py-2 bg-green-100 text-green-700 font-bold rounded-xl text-sm border-2 border-green-200 uppercase">Active
                    Record</span>
            @else
                <span
                    class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm border-2 border-slate-200 uppercase">{{ $asset->status }}</span>
            @endif
        </div>
        @if ($errors->any())
            <div
                class="mb-8 p-4 bg-red-50 border-l-4 border-red-600 text-red-800 rounded-r-lg font-bold flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <div class="lg:col-span-5 space-y-8">

                <div class="bg-white border-4 border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 p-4 border-b-2 border-slate-200 text-center">
                        <span class="text-sm font-bold text-slate-600 uppercase tracking-wide">Original ID
                            Capture</span>
                    </div>
                    <div class="p-6 bg-white flex items-center justify-center min-h-[300px]">
                        <img src="{{ $asset->image_url }}" alt="ID Card"
                            class="max-w-full max-h-[350px] object-contain rounded-xl drop-shadow-md">
                    </div>
                </div>

                <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-100 pb-4 mb-6">Database Matches
                    </h3>

                    @if ($student)
                        <div class="bg-green-50 border-2 border-green-200 p-5 rounded-xl flex items-center gap-5">
                            <div
                                class="w-14 h-14 bg-white rounded-xl flex items-center justify-center font-extrabold text-xl text-[#004d32] shadow-sm border border-green-200">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-lg font-bold text-green-900">{{ $student->name }}</p>
                                <p class="text-sm font-bold text-green-700 mt-1">{{ $student->id_number }}</p>
                                <p class="text-xs font-semibold text-green-600 mt-1 uppercase">
                                    {{ $student->program_code ?? 'Student Record' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 p-5 rounded-xl border-2 border-slate-100">
                            <p class="text-base font-bold text-slate-700">{{ $asset->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-sm h-fit">

                    <div class="flex items-center gap-4 border-b-2 border-slate-100 pb-6 mb-8">
                        <div
                            class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center shadow-inner shrink-0">
                            <svg class="w-6 h-6 text-[#FECB02]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Semantic Verification</h2>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">Upload a scan for AI validation, or use
                                the manual override.</p>
                        </div>
                    </div>

                    <form action="{{ route('assets.compare', $asset->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="border-4 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-slate-300 hover:bg-slate-50 transition-all cursor-pointer bg-slate-50 mb-6"
                            onclick="document.getElementById('comparison_image').click()" id="uploadWrapper">
                            <svg class="w-12 h-12 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                            </svg>
                            <span class="block text-sm font-bold text-slate-600">Click to upload physical ID scan</span>
                            <span class="block text-xs font-medium text-slate-400 mt-1">PNG, JPG up to 10MB</span>
                            <input type="file" name="compare_image" id="comparison_image" accept="image/*"
                                class="hidden">
                        </div>

                        <div id="cropperWrapper" class="hidden mb-6">
                            <div
                                class="h-[400px] bg-slate-900 rounded-2xl overflow-hidden border-4 border-slate-200 mb-4">
                                <img id="cropperImage" class="max-w-full">
                            </div>
                            <button type="button" id="cropButton"
                                class="w-full py-4 bg-slate-800 text-white font-bold text-base rounded-xl hover:bg-slate-700 transition-colors shadow-sm">
                                Confirm Area & Prepare Scan
                            </button>
                        </div>

                        <div id="previewWrapper" class="hidden mb-6 text-center">
                            <span class="block text-sm font-bold text-green-600 mb-4">Scan Ready for AI
                                Processing</span>
                            <div class="border-4 border-slate-200 rounded-2xl p-4 bg-white inline-block">
                                <img id="croppedPreview" class="h-48 object-contain rounded-lg">
                            </div>
                        </div>

                        <div class="mb-6 p-6 bg-slate-50 border-2 border-slate-200 rounded-2xl">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-[#004d32]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Manual Database
                                    Override</h3>
                            </div>
                            <p class="text-xs font-medium text-slate-500 mb-5 leading-relaxed">If the ID text is
                                illegible or you want to bypass the AI scan, manually type the Student Number below for
                                an instant database match.</p>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Student ID
                                        Number</label>
                                    <input type="text" name="manual_id" id="manual_id_input"
                                        placeholder="e.g. 202310501"
                                        class="w-full bg-white border-2 border-slate-200 rounded-xl p-3.5 text-sm font-bold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-colors shadow-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Student Name <span
                                                class="text-slate-400 font-medium">(Optional)</span></label>
                                        <input type="text" name="manual_name" placeholder="e.g. Amanda"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl p-3.5 text-sm font-semibold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-colors shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Program <span
                                                class="text-slate-400 font-medium">(Optional)</span></label>
                                        <input type="text" name="manual_program" placeholder="e.g. BSITWMA"
                                            class="w-full bg-white border-2 border-slate-200 rounded-xl p-3.5 text-sm font-semibold text-slate-800 focus:border-[#004d32] focus:ring-0 transition-colors shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="cropped_image" id="cropped_image">

                        <button type="submit" id="submitBtn" disabled
                            class="w-full py-5 bg-slate-900 text-white font-bold text-base rounded-xl opacity-50 cursor-not-allowed flex items-center justify-center gap-3 transition-all border-2 border-transparent">
                            Run Semantic Match Engine
                            <svg class="w-5 h-5 text-[#FECB02]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
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
            const manualIdInput = document.getElementById('manual_id_input');

            // --- Enable button if manual ID is typed ---
            if (manualIdInput) {
                manualIdInput.addEventListener('input', function() {
                    if (this.value.trim().length > 0) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.classList.add('hover:bg-slate-800', 'shadow-md');
                    } else if (!hiddenInput.value) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        submitBtn.classList.remove('hover:bg-slate-800', 'shadow-md');
                    }
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    uploadWrapper.style.display = 'none';
                    cropperWrapper.style.display = 'block';
                    previewWrapper.style.display = 'none';

                    // Keep disabled until cropped, unless manual ID is already typed
                    if (!manualIdInput.value.trim()) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        cropperImage.src = event.target.result;
                        if (cropper) {
                            cropper.destroy();
                        }
                        cropper = new Cropper(cropperImage, {
                            viewMode: 1,
                            responsive: true,
                            guides: true,
                            center: true,
                            highlight: false,
                            background: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (cropButton) {
                cropButton.addEventListener('click', function() {
                    if (!cropper) return;
                    const canvas = cropper.getCroppedCanvas({
                        maxWidth: 1024,
                        maxHeight: 1024,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    const base64 = canvas.toDataURL('image/jpeg', 0.95);
                    hiddenInput.value = base64;
                    croppedPreview.src = base64;

                    cropperWrapper.style.display = 'none';
                    previewWrapper.style.display = 'block';

                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.classList.add('hover:bg-slate-800', 'shadow-md');
                    }
                });
            }
        });
    </script>
</x-app-layout>
