<?php

namespace App\Http\Controllers;

use App\Models\CounselingReferral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounselingReferralController extends Controller
{
    /**
     * REFERRAL DASHBOARD
     * Display all active (non-archived) counseling referrals
     */
    public function index(Request $request)
    {
        $query = CounselingReferral::query();

        // Filter by administrative status if provided
        if ($status = $request->query('status')) {
            $query->where('administrative_status', $status);
        }

        // Get referrals with eager loaded relationships, latest first
        $referrals = $query->with(['student', 'referrer'])
                          ->latest()
                          ->get();

        // Unique statuses for filter display
        $statuses = [
            ['label' => 'Draft', 'value' => 'Draft'],
            ['label' => 'Forwarded to Guidance', 'value' => 'Forwarded to Guidance'],
            ['label' => 'Scheduled', 'value' => 'Scheduled'],
            ['label' => 'Resolved', 'value' => 'Resolved'],
        ];

        return view('referrals.index', compact('referrals', 'statuses'));
    }

    /**
     * CREATE REFERRAL FORM
     * Show form to create new counseling referral
     */
    public function create()
    {
        $students = User::where('role', 'student')
                       ->select('id', 'name', 'id_number', 'program_code')
                       ->orderBy('name')
                       ->get();

        return view('referrals.create', compact('students'));
    }

    /**
     * STORE REFERRAL
     * Save new counseling referral to database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'case_reference' => 'nullable|string|max:100',
            'support_urgency' => 'required|in:Standard,Priority,Immediate',
            'administrative_observations' => 'required|string|min:10',
        ]);

        // Auto-set referrer and initial status
        $validated['referrer_id'] = Auth::id();
        $validated['administrative_status'] = 'Draft';

        $referral = CounselingReferral::create($validated);

        return redirect()->route('referrals.show', $referral->id)
                       ->with('success', 'Supportive intervention referral created successfully.');
    }

    /**
     * VIEW REFERRAL DETAIL
     * Display referral with workflow actions
     */
    public function show($id)
    {
        $referral = CounselingReferral::with(['student', 'referrer'])->findOrFail($id);

        return view('referrals.show', compact('referral'));
    }

    /**
     * EDIT REFERRAL FORM
     * Show form to edit referral (Draft only)
     */
    public function edit($id)
    {
        $referral = CounselingReferral::findOrFail($id);

        // Only allow editing if in Draft status
        if ($referral->administrative_status !== 'Draft') {
            return back()->with('error', 'Referrals can only be edited while in Draft status.');
        }

        $students = User::where('role', 'student')
                       ->select('id', 'name', 'id_number', 'program_code')
                       ->orderBy('name')
                       ->get();

        return view('referrals.edit', compact('referral', 'students'));
    }

    /**
     * UPDATE REFERRAL
     * Update referral details (Draft only)
     */
    public function update(Request $request, $id)
    {
        $referral = CounselingReferral::findOrFail($id);

        // Verify still in Draft status
        if ($referral->administrative_status !== 'Draft') {
            return back()->with('error', 'Cannot update referral outside Draft status.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'case_reference' => 'nullable|string|max:100',
            'support_urgency' => 'required|in:Standard,Priority,Immediate',
            'administrative_observations' => 'required|string|min:10',
        ]);

        $referral->update($validated);

        return back()->with('success', 'Referral updated successfully.');
    }

    /**
     * SOFT DELETE REFERRAL
     * Archive referral (soft delete)
     */
    public function destroy($id)
    {
        $referral = CounselingReferral::findOrFail($id);
        $referral->delete();

        return redirect()->route('referrals.index')
                       ->with('success', 'Referral archived.');
    }

    /**
     * ARCHIVED REFERRALS
     * Display all soft-deleted referrals
     */
    public function archived()
    {
        $referrals = CounselingReferral::onlyTrashed()
                                       ->with(['student', 'referrer'])
                                       ->latest('deleted_at')
                                       ->get();

        return view('referrals.archives', compact('referrals'));
    }

    /**
     * RESTORE REFERRAL
     * Restore archived (soft-deleted) referral
     */
    public function restore($id)
    {
        $referral = CounselingReferral::withTrashed()->findOrFail($id);
        $referral->restore();

        return redirect()->route('referrals.archived')
                       ->with('success', 'Referral restored.');
    }

    /**
     * FORCE DELETE REFERRAL
     * Permanently delete referral (irreversible)
     */
    public function forceDelete($id)
    {
        $referral = CounselingReferral::withTrashed()->findOrFail($id);
        $referral->forceDelete();

        return redirect()->route('referrals.archived')
                       ->with('success', 'Referral permanently deleted.');
    }
}
