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
     * Converts digital captures into physical record images.
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
     */
    public function lostIds(Request $request)
    {
        $students = User::select('id', 'name', 'id_number')->get();
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
                preg_match('/\d{9}/', $item->description, $matches);
                $extractedId = !empty($matches) ? $matches[0] : '';

                if ($extractedId) {
                    $matchedStudent = $students->firstWhere('id_number', $extractedId);
                    if ($matchedStudent) {
                        $item->suggested_owner = $matchedStudent;
                        $item->confidence = 1.0;
                    }
                }
                return $item;
            });

        return view('assets.lost-ids', compact('ids', 'isHistory'));
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

        return redirect()->route('assets.lost-ids')->with('success', 'ID Identification successfully added to the vault.');
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
            return back()->with('info', 'Discovery Skipped: No pending ' . $searchType . ' reports currently exist in this category.');
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

        // FIXED ARGUMENT ORDER: (Target Image, Target Description, JSON Batch Path)
        // Matches Python: def process_batch(target_img_path, target_desc, batch_json_path)
        $processArgs = [
            $this->pythonPath,
            base_path('resources/scripts/visual_matcher.py'),
            $targetImagePath,
            $targetItem->description ?? 'No description.',
            $jsonFilePath
        ];

        $env = [
            'SystemRoot' => 'C:\\Windows',
            'windir'     => 'C:\\Windows',
            'HOME'       => 'C:\\Users\\Joss',
            'USERPROFILE' => 'C:\\Users\\Joss',
        ];

        $process = new Process($processArgs, null, $env);
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

        return back()->withErrors(['error' => 'Registry Intelligence Failure: ' . $process->getErrorOutput()]);
    }

    /**
     * RECORD FINALIZATION
     */
    public function confirmMatch($id)
    {
        $item = LostItem::findOrFail($id);
        $item->update(['status' => 'Claimed']);
        $route = ($item->item_category === 'ID / Identification') ? 'assets.lost-ids' : 'assets.index';
        return redirect()->route($route)->with('success', 'Asset record successfully resolved and marked as claimed.');
    }

    /**
     * ARCHIVE OVERSIGHT
     */
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
        return redirect()->route($route)->with('success', 'Record successfully archived.');
    }

    public function restore($id)
    {
        $item = LostItem::withTrashed()->findOrFail($id);
        $isId = $item->item_category === 'ID / Identification';
        $item->restore();
        $params = $isId ? ['category' => 'ID'] : [];
        return redirect()->route('assets.archived', $params)->with('success', 'Record restored to active registry.');
    }

    public function forceDelete($id)
    {
        $item = LostItem::withTrashed()->findOrFail($id);
        if ($item->image_path) { Storage::disk('public')->delete($item->image_path); }
        $isId = $item->item_category === 'ID / Identification';
        $item->forceDelete();
        $params = $isId ? ['category' => 'ID'] : [];
        return redirect()->route('assets.archived', $params)->with('success', 'Record permanently erased from server.');
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
}
