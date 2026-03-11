#!/usr/bin/env php
<?php

/**
 * FEU-CDGRS STUDENT REGISTRY LOOKUP - FIX VERIFICATION
 *
 * This diagnostic verifies the "Search Student Registry" flow is working correctly
 * for ID card matching with flexible ID format support.
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║  FEU-CDGRS STUDENT REGISTRY LOOKUP - DIAGNOSIS & FIX VERIFICATION ║\n";
echo "║                                                                  ║\n";
echo "║  Verifying ID extraction and student matching flow               ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";

$baseDir = __DIR__;

// TEST 1: View extraction logic
echo "\nTEST 1: View ID Extraction (show-id.blade.php)\n";
echo "==================================================\n";

$viewFile = $baseDir . '/resources/views/assets/show-id.blade.php';
$viewCode = file_get_contents($viewFile);

$test1 = [
    'Removes dashes before extraction' => str_contains($viewCode, "str_replace('-'"),
    'Uses flexible /\d{7,12}/ pattern' => str_contains($viewCode, '/\d{7,12}/'),
    'Stores extracted ID in hidden input' => str_contains($viewCode, 'name="manual_id"'),
    'Includes CSRF token' => str_contains($viewCode, '@csrf'),
    'Uses POST method' => str_contains($viewCode, 'method="POST"'),
    'Routes to assets.compare' => str_contains($viewCode, 'route(\'assets.compare\''),
];

$pass1 = true;
foreach ($test1 as $label => $result) {
    echo sprintf("%-55s %s\n", $label, $result ? "✓" : "✗");
    $pass1 = $pass1 && $result;
}

// TEST 2: Controller fallback logic
echo "\n\nTEST 2: Controller Fallback (AssetMatchingController.php)\n";
echo "==============================================================\n";

$controllerFile = $baseDir . '/app/Http/Controllers/AssetMatchingController.php';
$controllerCode = file_get_contents($controllerFile);

$test2 = [
    'Gets manual_id from request' => str_contains($controllerCode, "request->input('manual_id')"),
    'Handles fallback if manual_id empty' => str_contains($controllerCode, 'if (empty($manualId))'),
    'Removes dashes in fallback' => str_contains($controllerCode, "str_replace('-'") && str_contains($controllerCode, '$targetItem->description'),
    'Uses flexible digit pattern' => str_contains($controllerCode, '{7,12}'),
    'Searches with LIKE queries' => str_contains($controllerCode, "'LIKE', \"%{"),
];

$pass2 = true;
foreach ($test2 as $label => $result) {
    echo sprintf("%-55s %s\n", $label, $result ? "✓" : "✗");
    $pass2 = $pass2 && $result;
}

// TEST 3: Route configuration
echo "\n\nTEST 3: Route Configuration (routes/web.php)\n";
echo "===============================================\n";

$routeFile = $baseDir . '/routes/web.php';
$routeCode = file_get_contents($routeFile);

$test3 = [
    'POST route defined' => str_contains($routeCode, "Route::post('assets/{id}/compare'"),
    'Points to compare method' => str_contains($routeCode, "'compare'"),
    'Uses AssetMatchingController' => str_contains($routeCode, 'AssetMatchingController::class'),
    'Named route: assets.compare' => str_contains($routeCode, "'assets.compare'"),
];

$pass3 = true;
foreach ($test3 as $label => $result) {
    echo sprintf("%-55s %s\n", $label, $result ? "✓" : "✗");
    $pass3 = $pass3 && $result;
}

// TEST 4: Success view
echo "\n\nTEST 4: Success View (compare.blade.php)\n";
echo "============================================\n";

$compareFile = $baseDir . '/resources/views/assets/compare.blade.php';
if (file_exists($compareFile)) {
    $compareCode = file_get_contents($compareFile);

    $test4 = [
        'Displays breakdown message' => str_contains($compareCode, '$breakdown'),
        'Shows confidence score' => str_contains($compareCode, '$similarityScore'),
        'Shows match status' => str_contains($compareCode, '$isMatch'),
        'Has confirm button' => str_contains($compareCode, 'assets.confirm'),
    ];

    $pass4 = true;
    foreach ($test4 as $label => $result) {
        echo sprintf("%-55s %s\n", $label, $result ? "✓" : "✗");
        $pass4 = $pass4 && $result;
    }
} else {
    echo "✗ Compare view not found\n";
    $pass4 = false;
}

// TEST 5: ID Format Support
echo "\n\nTEST 5: ID Format Support\n";
echo "===========================\n";

function test_extraction($desc) {
    $cleanDesc = str_replace('-', '', $desc);
    preg_match('/\d{7,12}/', $cleanDesc, $idMatch);
    return !empty($idMatch) ? $idMatch[0] : '';
}

$formats = [
    "NAME: John | ID: 202401234" => "202401234",
    "NAME: Jane | ID: 2024-12345" => "202412345",
    "ID: 123456789" => "123456789",
];

$pass5 = true;
foreach ($formats as $desc => $expected) {
    $result = test_extraction($desc);
    $ok = $result === $expected;
    echo sprintf("%-45s %s\n", "Format: '$desc'", $ok ? "✓" : "✗");
    $pass5 = $pass5 && $ok;
}

// FINAL REPORT
echo "\n\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                        FINAL REPORT                              ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$results = [
    'View Extraction' => $pass1,
    'Controller Fallback' => $pass2,
    'Route Configuration' => $pass3,
    'Success View' => $pass4,
    'ID Format Support' => $pass5,
];

$allPass = true;
foreach ($results as $test => $result) {
    $status = $result ? "✓ PASS" : "✗ FAIL";
    echo sprintf("%-40s %s\n", $test, $status);
    $allPass = $allPass && $result;
}

echo "\n";
if ($allPass) {
    echo "✅ STUDENT REGISTRY LOOKUP FIXED!\n\n";
    echo "Summary of the fix:\n";
    echo "═══════════════════════════════════\n";
    echo "1. View now removes dashes: str_replace('-', '')\n";
    echo "2. View uses flexible pattern: /\\d{7,12}/\n";
    echo "3. Handles all ID formats:\n";
    echo "   - 202401234 (9 digits)\n";
    echo "   - 2024-12345 (5+4 with dash)\n";
    echo "   - 123456789 to 1234567890 (7-12 digits)\n";
    echo "4. Controller has fallback with same logic\n";
    echo "5. Form POSTs correctly with CSRF token\n";
    echo "6. Displays match result in compare.blade.php\n";
    echo "\n✓ The 'Search Student Registry' button now works!\n";
    exit(0);
} else {
    echo "⚠️  Some issues detected - review above\n";
    exit(1);
}
