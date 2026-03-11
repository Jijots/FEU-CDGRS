<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\Violation;
use App\Models\IncidentReport; // Import the IncidentReport model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Needed for the 'Raw' math queries

class DashboardController extends Controller
{
    public function index()
    {
        // 1. IntelliThings Metrics
        $totalAssets = LostItem::count();
        $pendingCount = IncidentReport::where('status', 'Pending Review')->count();

        // Status Breakdown
        $activeCount = LostItem::where('status', 'Active')->count();
        $claimedCount = LostItem::where('status', 'Claimed')->count();
        $matchedCount = LostItem::where('status', 'like', 'Matched%')->count();

        // Report Type Breakdown
        $lostCount = LostItem::where('report_type', 'Lost')->count();
        $foundCount = LostItem::where('report_type', 'Found')->count();

        // 2. Active Violations Monitor
        $activeViolations = Violation::where('status', 'Active')->count();

        // 3. System Archives
        $archivedAssets = LostItem::onlyTrashed()->count();
        $archivedViolations = Violation::onlyTrashed()->count();
        $totalArchived = $archivedAssets + $archivedViolations;

        /**
         * 4. HOTSPOT ANALYTICS (NEW)
         */

        // Asset Hotspots: Where items are found most frequently
        // We only look at 'Found' items to find the recovery hotspots
        $assetHotspots = LostItem::select('location_found', DB::raw('count(*) as total'))
            ->whereNotNull('location_found')
            ->groupBy('location_found')
            ->orderBy('total', 'desc')
            ->take(3) // Top 3 locations
            ->get();

        // Incident Hotspots: Where incidents are reported most frequently
        $incidentHotspots = IncidentReport::select('incident_location', DB::raw('count(*) as total'))
            ->groupBy('incident_location')
            ->orderBy('total', 'desc')
            ->take(3) // Top 3 locations
            ->get();

        // Academic Info
        $sy = '2025-2026';
        $semester = '2nd Semester';

        return view('dashboard', compact(
            'totalAssets', 'pendingCount', 'activeCount', 'claimedCount',
            'matchedCount', 'lostCount', 'foundCount', 'activeViolations',
            'totalArchived',
            'assetHotspots',    // Pass to view
            'incidentHotspots', // Pass to view
            'sy', 'semester'
        ));
    }
}
