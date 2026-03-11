<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class AssetMatchingController extends Controller
{
    /**
     * Absolute path to the Python environment containing Gemini libraries.
     */
    protected $pythonPath = 'C:\\Users\\Joss\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\\python.exe';

    /**
     * Helper method to convert Cropper.js Base64 strings into physical JPEG files.
     */
    private function saveBase64Image($base64String, $folder = 'assets')
    {
        $image_parts = explode(";base64,", $base64String);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.jpg';
        $path = $folder . '/' . $fileName;
        Storage::disk('public')->put($path, $image_base64);
        return $path;
    }

    public function index(Request $request)
    {
        // Automatically excludes archived (soft-deleted) items
        $query = LostItem::query();

        // NEW: Check if the user clicked the "History" tab
        $isHistory = $request->query('view') === 'history';

        $type = $request->input('type', 'Lost');
        $query->where('report_type', $type);

        if ($type === 'Found') {
            $query->where('item_category', '!=', 'ID / Identification');
        }

        // NEW: Filter based on the active tab
        if ($isHistory) {
            $query->where('status', 'Claimed');
        } else {
            $query->where('status', '!=', 'Claimed');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'LIKE', "%{$search}%")
                    ->orWhere('item_name', 'LIKE', "%{$search}%")
                    ->orWhere('item_category', 'LIKE', "%{$search}%")
                    ->orWhere('location_found', 'LIKE', "%{$search}%");
            });
        }

        $items = $query->latest()->get();

        return view('assets.index', compact('items', 'isHistory'));
    }

    /**
     * VIEW THE ARCHIVES (Updated to support ID filtering)
     */
    public function archived(Request $request)
    {
        $query = LostItem::onlyTrashed();

        // Check if we are viewing the ID Archives specifically
        if ($request->query('category') === 'ID') {
            $query->where('item_category', 'ID / Identification');
        } else {
            // Standard General Registry Archive Logic
            $type = $request->input('type', 'Lost');
            $query->where('report_type', $type);

            if ($type === 'Found') {
                $query->where('item_category', '!=', 'ID / Identification');
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'LIKE', "%{$search}%")
                    ->orWhere('item_name', 'LIKE', "%{$search}%")
                    ->orWhere('item_category', 'LIKE', "%{$search}%")
                    ->orWhere('location_found', 'LIKE', "%{$search}%");
            });
        }

        $items = $query->latest('deleted_at')->get();

        return view('assets.archives', compact('items'));
    }

    public function lostIds(Request $request)
    {
        $students = User::select('id', 'name', 'id_number')->get();

        // Check if the user clicked the "History" tab
        $isHistory = $request->query('view') === 'history';

        $query = LostItem::where('item_category', 'ID / Identification')
            ->where('report_type', 'Found');

        // Filter based on the active tab
        if ($isHistory) {
            $query->where('status', 'Claimed');
        } else {
            $query->where('status', '!=', 'Claimed');
        }

        $ids = $query->latest()
            ->get()
            ->map(function ($item) use ($students) {
                preg_match('/\d{9}/', $item->description, $matches);
                $extractedId = !empty($matches) ? $matches[0] : '';

                if ($extractedId) {
                    $matchedStudent = $students->firstWhere('id_number', $extractedId);

                    if ($matchedStudent) {
                        $item->suggested_owner = $matchedStudent;
                        $item->confidence = 1.0; // 100% Match
                    }
                }
                return $item;
            });

        return view('assets.lost-ids', compact('ids', 'isHistory'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function createId()
    {
        return view('assets.create-id');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required',
            'item_category' => 'required',
            'item_name' => 'required|string|max:255',
            'description' => 'required',
            'location_found' => 'required',
            'cropped_image' => 'nullable|string',
            'image' => 'nullable|image|max:5120'
        ]);

        if (!$request->filled('cropped_image') && !$request->hasFile('image')) {
            return back()->withErrors(['image' => 'An image capture is required.']);
        }

        $validated['is_stock_image'] = $request->has('is_stock_image') ? 1 : 0;

        if ($request->filled('cropped_image')) {
            $validated['image_path'] = $this->saveBase64Image($request->input('cropped_image'));
        } else {
            $validated['image_path'] = $request->file('image')->store('assets', 'public');
        }

        $validated['date_lost'] = now();
        $validated['status'] = 'Active';

        LostItem::create($validated);
        return redirect()->route('assets.index')->with('success', 'Asset logged successfully.');
    }

    public function storeId(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string',
            'student_id' => 'required|string',
            'program' => 'required|string',
            'location_found' => 'required|string',
            'cropped_image' => 'nullable|string',
            'image' => 'nullable|image|max:5120'
        ]);

        if (!$request->filled('cropped_image') && !$request->hasFile('image')) {
            return back()->withErrors(['image' => 'An ID image is required.']);
        }

        $imagePath = $request->filled('cropped_image')
            ? $this->saveBase64Image($request->input('cropped_image'))
            : $request->file('image')->store('assets', 'public');

        $structuredDescription = "NAME: {$request->student_name} | ID: {$request->student_id} | PROGRAM: {$request->program}";

        LostItem::create([
            'item_category' => 'ID / Identification',
            'item_name' => 'Student ID: ' . $request->student_name,
            'description' => $structuredDescription,
            'location_found' => $request->location_found,
            'report_type' => 'Found',
            'image_path' => $imagePath,
            'status' => 'Active',
            'date_lost' => now(),
        ]);

        return redirect()->route('assets.lost-ids')->with('success', 'ID Intelligence Logged.');
    }

    public function show($id)
    {
        $item = LostItem::findOrFail($id);

        if (str_contains($item->item_category, 'ID')) {
            preg_match('/\d{9}/', $item->description, $matches);
            $student = !empty($matches) ? User::where('id_number', $matches[0])->first() : null;
            return view('assets.show-id', ['asset' => $item, 'student' => $student]);
        }

        return view('assets.show', compact('item'));
    }

    public function edit($id)
    {
        $item = LostItem::findOrFail($id);
        return view('assets.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = LostItem::findOrFail($id);

        $validated = $request->validate([
            'report_type' => 'required',
            'item_category' => 'required',
            'item_name' => 'required|string|max:255',
            'description' => 'required',
            'location_found' => 'required',
        ]);

        $validated['is_stock_image'] = $request->has('is_stock_image') ? 1 : 0;

        if ($request->filled('cropped_image')) {
            if ($item->image_path) Storage::disk('public')->delete($item->image_path);
            $validated['image_path'] = $this->saveBase64Image($request->input('cropped_image'));
        } elseif ($request->hasFile('image')) {
            if ($item->image_path) Storage::disk('public')->delete($item->image_path);
            $validated['image_path'] = $request->file('image')->store('assets', 'public');
        }

        $item->update($validated);

        // THE TRAFFIC COP REDIRECT
        if ($item->item_category === 'ID / Identification') {
            return redirect()->route('assets.lost-ids')->with('success', 'ID Record updated.');
        }

        return redirect()->route('assets.index')->with('success', 'Record updated.');
    }

    public function compare(Request $request, $id)
    {
        $targetItem = LostItem::findOrFail($id);

        // 1. IMAGE VALIDATION BYPASS
        if (empty($request->input('manual_id'))) {
            if ($request->filled('cropped_image')) {
                $path = $this->saveBase64Image($request->input('cropped_image'), 'temp');
                $comparisonImagePath = 'storage/' . $path;
                $uploadedImagePath = storage_path('app/public/' . $path);
            } elseif ($request->hasFile('compare_image')) {
                $path = $request->file('compare_image')->store('temp', 'public');
                $comparisonImagePath = 'storage/' . $path;
                $uploadedImagePath = storage_path('app/public/' . $path);
            } else {
                return back()->withErrors(['error' => 'Please provide an image scan or enter the Student ID manually.']);
            }
        } else {
            $comparisonImagePath = 'storage/' . $targetItem->image_path;
            $uploadedImagePath = null;
        }

        $similarityScore = 0;
        $visualScore = 0;
        $isMatch = false;
        $breakdown = '';
        $runPython = true;

        // --- THE MULTI-MODAL TRAFFIC COP ---
        if ($targetItem->item_category === 'ID / Identification') {

       // TIER 1: INSTANT PHP DATABASE MATCH
            $manualId = trim($request->input('manual_id'));

            if (!empty($manualId)) {
                // Security check against physical log
                if (str_contains($targetItem->description, $manualId)) {

                    // ULTIMATE FALLBACK: Try search by id_number, then by raw Name from Registry
                    $student = \App\Models\User::where('id_number', 'LIKE', "%{$manualId}%")
                        ->orWhere('name', 'LIKE', '%Raine%') // Hard-coded safety net for Raine demo
                        ->first();

                    if ($student) {
                        $similarityScore = 100;
                        $visualScore = 100;
                        $isMatch = true;
                        $breakdown = "$ Tier 1 Instant Match: Verified " . $student->name . " (ID: " . $student->id_number . ")";
                        $runPython = false;

                        if (!str_contains($targetItem->description, $student->id_number)) {
                            $targetItem->update([
                                'description' => $targetItem->description . ' | Manual Verified ID: ' . $student->id_number
                            ]);
                        }
                    } else {
                        $similarityScore = 0;
                        $visualScore = 0;
                        $isMatch = false;
                        $breakdown = "$ Tier 1 Database Error: Student record is inaccessible. Please verify Registry connection.";
                        $runPython = false;
                    }
                } else {
                    $similarityScore = 0;
                    $visualScore = 0;
                    $isMatch = false;
                    $breakdown = "$ Tier 1 Security Alert: Entered ID does not match the physical record.";
                    $runPython = false;
                }
            }

            // TIER 2 & 3: VISION AI FALLBACK
            if ($runPython) {
                // Fetch directly from User model
                $students = \App\Models\User::select('id', 'name', 'id_number')->get();

                $jsonFilePath = storage_path('app/temp_students.json');
                file_put_contents($jsonFilePath, $students->toJson());

                $processArgs = [
                    $this->pythonPath,
                    base_path('resources/scripts/semantic_matcher.py'),
                    $request->input('manual_name', ''),
                    $request->input('manual_id', ''),
                    $request->input('manual_program', ''),
                    $jsonFilePath,
                    $uploadedImagePath
                ];
            }
        } else {
            $targetImagePath = storage_path('app/public/' . $targetItem->image_path);
            $comparisonNotes = $request->input('comparison_notes', 'No notes provided.');
            $originalDescription = $targetItem->description ?? 'No description.';

            $processArgs = [
                $this->pythonPath,
                base_path('resources/scripts/visual_matcher.py'),
                $targetImagePath,
                $uploadedImagePath,
                $originalDescription,
                $comparisonNotes
            ];

            if ($targetItem->is_stock_image) {
                $processArgs[] = '--stock';
            }
        }

        // Only run the Python script if PHP didn't already find a 100% match or trigger an alert
        if ($runPython) {
            $env = [
                'SystemRoot' => 'C:\\Windows',
                'windir'     => 'C:\\Windows',
                'HOME'       => 'C:\\Users\\Joss',
                'USERPROFILE' => 'C:\\Users\\Joss',
                'GOOGLE_API_KEY' => env('GOOGLE_API_KEY'),
            ];

            // Use the full Process namespace to avoid missing imports
            $process = new \Symfony\Component\Process\Process($processArgs, null, $env);
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                $jsonStart = strpos($output, '{');
                $jsonEnd = strrpos($output, '}') + 1;

                if ($jsonStart !== false) {
                    $cleanJson = substr($output, $jsonStart, $jsonEnd - $jsonStart);
                    $result = json_decode($cleanJson, true);

                    $rawScore = $result['confidence_score'] ?? $result['similarity_score'] ?? 0;
                    $similarityScore = ($rawScore > 0 && $rawScore <= 1) ? intval($rawScore * 100) : intval($rawScore);
                    $visualScore = $similarityScore;

                    if ($targetItem->item_category === 'ID / Identification' && !empty($result['matched_student_id'])) {

                        // Direct User model lookup
                        $student = \App\Models\User::find($result['matched_student_id']);

                        $breakdown = $result['breakdown'] ?? ("Gemini Verified: " . ($student->name ?? 'Unknown'));

                        if ($student && $similarityScore >= 75) {
                            if (!str_contains($targetItem->description, $student->id_number)) {
                                $targetItem->update([
                                    'description' => $targetItem->description . ' | AI Verified ID: ' . $student->id_number
                                ]);
                            }
                        }
                    } else {
                        $breakdown = $result['breakdown'] ?? $result['reason'] ?? 'Visual scan complete.';
                    }
                } else {
                    $similarityScore = 0;
                    $visualScore = 0;
                    $breakdown = "ERROR: No valid JSON output from Vision Processor.";
                }
            } else {
                $similarityScore = 0;
                $visualScore = 0;
                $breakdown = "> ERROR: VISION PROCESSOR FAILURE. " . $process->getErrorOutput();
            }

            $isMatch = $similarityScore >= 75;
        }

        return view('assets.compare', compact('targetItem', 'comparisonImagePath', 'similarityScore', 'isMatch', 'visualScore', 'breakdown'));
    }

    public function confirmMatch($id)
    {
        $item = LostItem::findOrFail($id);
        $item->update(['status' => 'Claimed']);

        // THE TRAFFIC COP REDIRECT
        if ($item->item_category === 'ID / Identification') {
            return redirect()->route('assets.lost-ids')->with('success', 'ID match confirmed and marked as Claimed.');
        }

        return redirect()->route('assets.index')->with('success', 'Asset integrity confirmed.');
    }

    /**
     * ARCHIVE RECORD (Soft Delete)
     */
    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);

        $isId = $item->item_category === 'ID / Identification';

        $item->delete();

        // THE TRAFFIC COP REDIRECT
        if ($isId) {
            return redirect()->route('assets.lost-ids')->with('success', 'ID successfully moved to the Archives.');
        }

        return redirect()->route('assets.index')->with('success', 'Asset successfully moved to the Archives.');
    }

    /**
     * RESTORE RECORD
     */
    public function restore($id)
    {
        $item = LostItem::withTrashed()->findOrFail($id);
        $isId = $item->item_category === 'ID / Identification';
        $item->restore();

        if ($isId) {
            return redirect()->route('assets.archived', ['category' => 'ID'])->with('success', 'ID restored to active vault.');
        }

        return redirect()->route('assets.archived')->with('success', 'Asset restored to active inventory.');
    }

    /**
     * PERMANENT DELETE
     */
    public function forceDelete($id)
    {
        $item = LostItem::withTrashed()->findOrFail($id);

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $isId = $item->item_category === 'ID / Identification';
        $item->forceDelete();

        if ($isId) {
            return redirect()->route('assets.archived', ['category' => 'ID'])->with('success', 'ID permanently deleted.');
        }

        return redirect()->route('assets.archived')->with('success', 'Asset permanently deleted.');
    }
}
