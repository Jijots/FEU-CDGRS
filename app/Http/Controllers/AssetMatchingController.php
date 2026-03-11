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
     * Absolute path to the local Registry Intelligence (Python) environment.
     */
    protected $pythonPath = 'C:\\Users\\Joss\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\\python.exe';

    /**
     * Helper: Converts digital captures into physical record images.
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

    /**
     * PRIMARY REGISTRY VIEW
     */
    public function index(Request $request)
    {
        $query = LostItem::query();
        $isHistory = $request->query('view') === 'history';
        $type = $request->input('type', 'Lost');

        $query->where('report_type', $type);

        if ($type === 'Found') {
            $query->where('item_category', '!=', 'ID / Identification');
        }

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
     * ID RECOVERY VAULT MANAGEMENT
     * Fixed the "Unidentified Record" issue by handling dashes and multi-digit patterns.
     */
    public function lostIds(Request $request)
    {
        $students = User::where('role', 'student')
                       ->select('id', 'name', 'id_number')
                       ->get();
        $isHistory = $request->query('view') === 'history';

        $query = LostItem::where('item_category', 'ID / Identification')
            ->where('report_type', 'Found');

        if ($isHistory) {
            $query->where('status', 'Claimed');
        } else {
            $query->where('status', '!=', 'Claimed');
        }

        $ids = $query->latest()
            ->get()
            ->map(function ($item) use ($students) {
                // Remove dashes and look for a sequence of 7-12 digits
                $cleanDesc = str_replace('-', '', $item->description);
                preg_match('/\d{7,12}/', $cleanDesc, $matches);
                $extractedId = !empty($matches) ? $matches[0] : '';

                if ($extractedId) {
                    $matchedStudent = $students->first(function($s) use ($extractedId) {
                        return str_replace('-', '', $s->id_number) === $extractedId;
                    });

                    if ($matchedStudent) {
                        $item->suggested_owner = $matchedStudent;
                    }
                }
                return $item;
            });

        return view('assets.lost-ids', compact('ids', 'isHistory'));
    }

    /**
     * IDENTITY REGISTRY VERIFICATION (The matching process)
     * Fixed the refresh-loop by providing error feedback when a match fails.
     */
    public function compare(Request $request, $id)
    {
        $targetItem = LostItem::findOrFail($id);
        $manualId = trim($request->input('manual_id'));
        $selectedStudentId = $request->input('student_id');

        // If student manually selected, use that directly
        if ($selectedStudentId) {
            $student = User::findOrFail($selectedStudentId);
            return view('assets.compare', [
                'targetItem' => $targetItem,
                'comparisonImagePath' => 'storage/' . $targetItem->image_path,
                'similarityScore' => 100,
                'isMatch' => true,
                'visualScore' => 100,
                'breakdown' => "Registry Intelligence Match: Verified " . $student->name . " (#" . $student->id_number . ")"
            ]);
        }

        // If hidden ID extraction failed in the view, try one last time here
        if (empty($manualId)) {
            $cleanDesc = str_replace('-', '', $targetItem->description);
            preg_match('/\d{7,12}/', $cleanDesc, $matches);
            $manualId = !empty($matches) ? $matches[0] : '';
        }

        if (!empty($manualId)) {
            $cleanId = str_replace('-', '', $manualId);
            $student = User::where('role', 'student')
                           ->where('id_number', 'LIKE', "%{$cleanId}%")
                           ->first();

            if ($student) {
                return view('assets.compare', [
                    'targetItem' => $targetItem,
                    'comparisonImagePath' => 'storage/' . $targetItem->image_path,
                    'similarityScore' => 100,
                    'isMatch' => true,
                    'visualScore' => 100,
                    'breakdown' => "Registry Intelligence Match: Verified " . $student->name . " (#" . $student->id_number . ")"
                ]);
            }

            // Strategy 2: Try name match from description if numeric ID match fails
            $nameParts = [];
            if (preg_match('/NAME:\s*([^|]+)/', $targetItem->description, $matches)) {
                $extractedName = trim($matches[1]);
                $parts = explode(' ', $extractedName);

                // Try second-to-last word (actual last name, before suffix)
                if (count($parts) >= 2) {
                    $lastName = $parts[count($parts) - 2];
                    $student = User::where('role', 'student')
                                   ->where('name', 'LIKE', "%{$lastName}%")
                                   ->first();

                    if ($student) {
                        return view('assets.compare', [
                            'targetItem' => $targetItem,
                            'comparisonImagePath' => 'storage/' . $targetItem->image_path,
                            'similarityScore' => 100,
                            'isMatch' => true,
                            'visualScore' => 100,
                            'breakdown' => "Registry Intelligence Match: Verified " . $student->name . " (#" . $student->id_number . ")"
                        ]);
                    }
                }

                // If still not found, try any substantial part
                if (!$student && count($parts) >= 2) {
                    $student = User::where('role', 'student')
                                   ->where(function($q) use ($parts) {
                                       foreach ($parts as $part) {
                                           if (strlen($part) > 3) {
                                               $q->orWhere('name', 'LIKE', "%{$part}%");
                                           }
                                       }
                                   })
                                   ->first();

                    if ($student) {
                        return view('assets.compare', [
                            'targetItem' => $targetItem,
                            'comparisonImagePath' => 'storage/' . $targetItem->image_path,
                            'similarityScore' => 100,
                            'isMatch' => true,
                            'visualScore' => 100,
                            'breakdown' => "Registry Intelligence Match: Verified " . $student->name . " (#" . $student->id_number . ")"
                        ]);
                    }
                }
            }
        }

        // Auto-match failed - show manual selection interface with students only
        $allStudents = User::where('role', 'student')
                          ->select('id', 'name', 'id_number')
                          ->orderBy('name')
                          ->get();

        return back()->with('showStudentSelection', true)
                     ->with('availableStudents', $allStudents)
                     ->with('extractedId', $manualId ?: 'Not detected');
    }

    /**
     * ASSET DISCOVERY ENGINE (1-to-Many Batch Search)
     */
    public function findMatches($id)
    {
        $targetItem = LostItem::findOrFail($id);
        $searchType = $targetItem->report_type === 'Found' ? 'Lost' : 'Found';

        $candidates = LostItem::where('report_type', $searchType)
            ->where('item_category', $targetItem->item_category)
            ->where('status', '!=', 'Claimed')
            ->get();

        if ($candidates->isEmpty()) {
            return back()->with('info', 'Discovery Skipped: No matching ' . $searchType . ' reports currently exist in this category.');
        }

        $batchData = [];
        foreach ($candidates as $candidate) {
            $batchData[] = [
                'id' => $candidate->id,
                'image_path' => storage_path('app/public/' . str_replace(['public/', 'storage/'], '', $candidate->image_path))
            ];
        }

        $jsonFilePath = storage_path('app/temp_batch.json');
        file_put_contents($jsonFilePath, json_encode($batchData));
        $targetImagePath = storage_path('app/public/' . str_replace(['public/', 'storage/'], '', $targetItem->image_path));

        // REGISTRY INTELLIGENCE: Invoke Visual Matching Engine
        $processArgs = [
            $this->pythonPath,
            base_path('resources/scripts/visual_matcher.py'),
            $targetImagePath,
            $jsonFilePath
        ];

        $process = new Process($processArgs, null, [
            'SystemRoot' => 'C:\\Windows',
            'windir'     => 'C:\\Windows',
            'HOME'       => 'C:\\Users\\Joss',
            'USERPROFILE' => 'C:\\Users\\Joss',
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $jsonStart = strpos($output, '{');
            if ($jsonStart !== false) {
                $result = json_decode(substr($output, $jsonStart), true);
                if (isset($result['error']) && $result['error']) {
                    return back()->withErrors(['error' => 'System Insight: ' . $result['message']]);
                }

                $matches = $result['matches'] ?? [];
                foreach ($matches as &$match) {
                    $match['model'] = LostItem::find($match['item_id']);
                }
                return view('assets.matches', compact('targetItem', 'matches'));
            }
        }

        return back()->withErrors(['error' => 'Discovery Intelligence Failure: ' . $process->getErrorOutput()]);
    }

    /**
     * REGISTRY ENTRY ACTIONS
     */
    public function create() { return view('assets.create'); }
    public function createId() { return view('assets.create-id'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required',
            'item_category' => 'required',
            'item_name' => 'required|string|max:255',
            'description' => 'required',
            'location_found' => 'required',
            'image' => 'required|image|max:5120'
        ]);

        $validated['image_path'] = $request->file('image')->store('assets', 'public');
        $validated['date_lost'] = now();
        $validated['status'] = 'Active';

        LostItem::create($validated);
        return redirect()->route('assets.index')->with('success', 'Asset successfully logged in the registry.');
    }

    public function storeId(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string',
            'student_id' => 'required|string',
            'program' => 'required|string',
            'location_found' => 'required|string',
            'cropped_image' => 'required|string'
        ]);

        $imagePath = $this->saveBase64Image($request->input('cropped_image'));
        $description = "NAME: {$request->student_name} | ID: {$request->student_id} | PROGRAM: {$request->program}";

        LostItem::create([
            'item_category' => 'ID / Identification',
            'item_name' => 'Student ID: ' . $request->student_name,
            'description' => $description,
            'location_found' => $request->location_found,
            'report_type' => 'Found',
            'image_path' => $imagePath,
            'status' => 'Active',
            'date_lost' => now(),
        ]);

        return redirect()->route('assets.lost-ids')->with('success', 'ID Identification added to the vault.');
    }

    public function show($id)
    {
        $item = LostItem::findOrFail($id);
        if (str_contains($item->item_category, 'ID')) {
            $cleanDesc = str_replace('-', '', $item->description);
            preg_match('/\d{7,12}/', $cleanDesc, $matches);
            $extractedId = !empty($matches) ? $matches[0] : '';

            // Strategy 1: Try numeric ID match
            $student = $extractedId ? User::where('role', 'student')
                                          ->where('id_number', 'LIKE', "%{$extractedId}%")
                                          ->first() : null;

            // Strategy 2: Try name-based match if numeric ID doesn't work
            if (!$student && preg_match('/NAME:\s*([^|]+)/', $item->description, $nameMatch)) {
                $extractedName = trim($nameMatch[1]);
                $parts = explode(' ', $extractedName);

                // Try multiple name parts (skip suffixes like Jr., Sr.)
                if (count($parts) >= 2) {
                    // Try second-to-last word (actual last name, before suffix)
                    $lastName = $parts[count($parts) - 2];
                    $student = User::where('role', 'student')
                                   ->where('name', 'LIKE', "%{$lastName}%")
                                   ->first();
                }

                // If still not found, try any substantial part
                if (!$student && count($parts) >= 2) {
                    $student = User::where('role', 'student')
                                   ->where(function($q) use ($parts) {
                                       foreach ($parts as $part) {
                                           if (strlen($part) > 3) { // Skip short parts like Jr, Sr, II
                                               $q->orWhere('name', 'LIKE', "%{$part}%");
                                           }
                                       }
                                   })
                                   ->first();
                }
            }

            return view('assets.show-id', ['asset' => $item, 'student' => $student]);
        }
        return view('assets.show', compact('item'));
    }

    public function edit($id) { return view('assets.edit', ['item' => LostItem::findOrFail($id)]); }

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
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($item->image_path);
            $validated['image_path'] = $request->file('image')->store('assets', 'public');
        }
        $item->update($validated);
        $route = ($item->item_category === 'ID / Identification') ? 'assets.lost-ids' : 'assets.index';
        return redirect()->route($route)->with('success', 'Registry record updated.');
    }

    public function confirmMatch($id)
    {
        $item = LostItem::findOrFail($id);
        $item->update(['status' => 'Claimed']);
        $route = ($item->item_category === 'ID / Identification') ? 'assets.lost-ids' : 'assets.index';
        return redirect()->route($route)->with('success', 'Asset record finalized and marked as claimed.');
    }

    public function archived(Request $request)
    {
        $query = LostItem::onlyTrashed();
        if ($request->query('category') === 'ID') { $query->where('item_category', 'ID / Identification'); }
        else {
            $type = $request->input('type', 'Lost');
            $query->where('report_type', $type);
            if ($type === 'Found') { $query->where('item_category', '!=', 'ID / Identification'); }
        }
        $items = $query->latest('deleted_at')->get();
        return view('assets.archives', compact('items'));
    }

    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);
        $isId = $item->item_category === 'ID / Identification';
        $item->delete();
        $route = $isId ? 'assets.lost-ids' : 'assets.index';
        return redirect()->route($route)->with('success', 'Record archived.');
    }

    public function restore($id)
    {
        $item = LostItem::withTrashed()->findOrFail($id);
        $isId = $item->item_category === 'ID / Identification';
        $item->restore();
        $params = $isId ? ['category' => 'ID'] : [];
        return redirect()->route('assets.archived', $params)->with('success', 'Record restored.');
    }

    public function forceDelete($id)
    {
        $item = LostItem::withTrashed()->findOrFail($id);
        if ($item->image_path) { Storage::disk('public')->delete($item->image_path); }
        $item->forceDelete();
        $isId = $item->item_category === 'ID / Identification';
        $params = $isId ? ['category' => 'ID'] : [];
        return redirect()->route('assets.archived', $params)->with('success', 'Record permanently deleted.');
    }
}
