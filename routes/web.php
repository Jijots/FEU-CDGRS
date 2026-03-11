<?php

use App\Http\Controllers\AssetMatchingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\ViolationReportController;
use App\Http\Controllers\ConfiscatedItemController;
use App\Http\Controllers\IncidentReportController; // Added this import
use App\Http\Controllers\CounselingReferralController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

// 1. ROOT REDIRECT
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. AUTHENTICATED SYSTEM ROUTES
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- INTELLITHINGS MODULE (Asset Management & AI Matching) ---
    // ARCHIVE ROUTES (Must be above resource/id routes)
    Route::get('/assets/{id}/find-matches', [App\Http\Controllers\AssetMatchingController::class, 'findMatches'])->name('assets.find-matches');
    Route::get('/assets/archived', [AssetMatchingController::class, 'archived'])->name('assets.archived');
    Route::post('/assets/{id}/restore', [AssetMatchingController::class, 'restore'])->name('assets.restore');
    Route::delete('/assets/{id}/force-delete', [AssetMatchingController::class, 'forceDelete'])->name('assets.force-delete');

    Route::post('assets/{id}/compare', [AssetMatchingController::class, 'compare'])->name('assets.compare');
    Route::post('assets/{id}/confirm', [AssetMatchingController::class, 'confirmMatch'])->name('assets.confirm');
    Route::get('/assets/id-recovery', [AssetMatchingController::class, 'lostIds'])->name('assets.lost-ids');
    Route::get('/assets/id-recovery/create', [AssetMatchingController::class, 'createId'])->name('assets.create-id');
    Route::post('/assets/id-recovery/store', [AssetMatchingController::class, 'storeId'])->name('assets.store-id');
    // Resource handles: index, create, store, show, edit, update, destroy (Soft Delete)
    Route::resource('assets', AssetMatchingController::class);

    // --- EVIDENCE LOCKER (Confiscated Items) ---
    // ARCHIVE ROUTES
    Route::get('/confiscated-items/archived', [ConfiscatedItemController::class, 'archived'])->name('confiscated-items.archived');
    Route::post('/confiscated-items/{id}/restore', [ConfiscatedItemController::class, 'restore'])->name('confiscated-items.restore');
    Route::delete('/confiscated-items/{id}/force-delete', [ConfiscatedItemController::class, 'forceDelete'])->name('confiscated-items.force-delete');
    // Resource Routes
    Route::resource('confiscated-items', ConfiscatedItemController::class);

    // --- STUDENT PROFILES & SEARCH ---
    Route::get('/students', [StudentProfileController::class, 'index'])->name('students.index');
    Route::post('/students/import', [StudentProfileController::class, 'import'])->name('students.import');
    Route::get('/students/{student}', [StudentProfileController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentProfileController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentProfileController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentProfileController::class, 'destroy'])->name('students.destroy');

    // --- DISCIPLINARY HUB (Violations) ---
    Route::get('/violations/report', [ViolationReportController::class, 'index'])->name('violations.report');

    // ARCHIVE ROUTES (Must be above /{id} routes)
    Route::get('/violations/archived', [ViolationController::class, 'archived'])->name('violations.archived');
    Route::post('/violations/{id}/restore', [ViolationController::class, 'restore'])->name('violations.restore');
    Route::delete('/violations/{id}/force-delete', [ViolationController::class, 'forceDelete'])->name('violations.force-delete');

    Route::get('/violations/create', [ViolationController::class, 'create'])->name('violations.create');
    Route::post('/violations', [ViolationController::class, 'store'])->name('violations.store');
    Route::get('/violations/{id}/nte', [ViolationController::class, 'generateNTE'])->name('violations.nte');
    Route::get('/violations/{id}', [ViolationController::class, 'show'])->name('violations.show');
    Route::get('/violations/{id}/edit', [ViolationController::class, 'edit'])->name('violations.edit');
    Route::put('/violations/{id}', [ViolationController::class, 'update'])->name('violations.update');
    Route::delete('/violations/{id}', [ViolationController::class, 'destroy'])->name('violations.destroy'); // Soft Delete

    // --- INCIDENT REPORTS ---
    // ARCHIVE ROUTES
    Route::get('/incidents/archived', [IncidentReportController::class, 'archived'])->name('incidents.archived');
    Route::post('/incidents/{id}/restore', [IncidentReportController::class, 'restore'])->name('incidents.restore');
    Route::delete('/incidents/{id}/force-delete', [IncidentReportController::class, 'forceDelete'])->name('incidents.force-delete');
    // Resource Routes
    Route::resource('incidents', IncidentReportController::class);

    // --- GUIDANCE & COUNSELING REFERRALS ---
    // ARCHIVE ROUTES (Must be above resource/id routes)
    Route::get('/referrals/archived', [CounselingReferralController::class, 'archived'])->name('referrals.archived');
    Route::post('/referrals/{id}/restore', [CounselingReferralController::class, 'restore'])->name('referrals.restore');
    Route::delete('/referrals/{id}/force-delete', [CounselingReferralController::class, 'forceDelete'])->name('referrals.force-delete');
    // Resource Routes (handles index, create, store, show, edit, update, destroy)
    Route::resource('referrals', CounselingReferralController::class);

    // --- USER ACCOUNT MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- REPORTS ---
    Route::get('/reports/hotspots', [ReportController::class, 'exportHotspots'])->name('reports.hotspots');
});

require __DIR__ . '/auth.php';
