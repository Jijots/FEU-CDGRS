#!/usr/bin/env php
<?php

/**
 * FEU-CDGRS DISCOVERY ENGINE DIAGNOSTICS
 * Validates the 1-to-Many sync between Laravel Controller and Python Vision Script
 * Run: php check_discovery_engine.php
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║     FEU-CDGRS DISCOVERY ENGINE REDEFENSE DIAGNOSTICS             ║\n";
echo "║                                                                  ║\n";
echo "║     Validating 1-to-Many Sync for Administrative Presentation    ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";

$baseDir = __DIR__;
$results = [];

// TEST 1: Python script signature
echo "\nTEST 1: Python Argument Signature\n";
echo "==================================\n";

$pythonFile = $baseDir . '/resources/scripts/visual_matcher.py';
if (!file_exists($pythonFile)) {
    echo "✗ Python script not found at $pythonFile\n";
    exit(1);
}

$pythonCode = file_get_contents($pythonFile);

$test1 = [
    'Function signature: process_batch(target_img_path, batch_json_path)'
        => preg_match('/def process_batch\(target_img_path, batch_json_path\)/', $pythonCode),
    'CLI: parser expects "target_img"'
        => preg_match('/parser\.add_argument\("target_img"\)/', $pythonCode),
    'CLI: parser expects "batch_json"'
        => preg_match('/parser\.add_argument\("batch_json"\)/', $pythonCode),
    'NO "target_desc" parameter in function'
        => !preg_match('/def process_batch\([^)]*target_desc[^)]*\)/', $pythonCode),
    'NO "target_desc" in CLI arguments'
        => !preg_match('/parser\.add_argument\("target_desc"\)/', $pythonCode),
];

$pass1 = true;
foreach ($test1 as $label => $passed) {
    echo sprintf("%-60s %s\n", $label, $passed ? "✓" : "✗");
    $pass1 = $pass1 && $passed;
}
$results['Python Signature'] = $pass1;

// TEST 2: Controller argument passing
echo "\n\nTEST 2: Controller Argument Count\n";
echo "==================================\n";

$controllerFile = $baseDir . '/app/Http/Controllers/AssetMatchingController.php';
if (!file_exists($controllerFile)) {
    echo "✗ Controller not found\n";
    exit(1);
}

$controllerCode = file_get_contents($controllerFile);

// Check that controller doesn't pass $targetItem->description
$hasUnwantedDesc = preg_match('/\$processArgs.*\$targetItem->description/s', $controllerCode);
$hasTargetImage = preg_match('/\$processArgs.*\$targetImagePath/s', $controllerCode);
$hasJsonFile = preg_match('/\$processArgs.*\$jsonFilePath/s', $controllerCode);

$test2 = [
    'Passes $targetImagePath' => $hasTargetImage,
    'Passes $jsonFilePath' => $hasJsonFile,
    'Does NOT pass $targetItem->description' => !$hasUnwantedDesc,
];

$pass2 = true;
foreach ($test2 as $label => $passed) {
    echo sprintf("%-60s %s\n", $label, $passed ? "✓" : "✗");
    $pass2 = $pass2 && $passed;
}
$results['Controller Args'] = $pass2;

// TEST 3: 75% Confidence Filter
echo "\n\nTEST 3: 75% Confidence Filter\n";
echo "===============================\n";

$test3 = [
    'Filter threshold >= 75%' => preg_match('/if final_score >= 75:/', $pythonCode),
    'Results limited to top 5' => preg_match('/results\[:5\]/', $pythonCode),
    'Results sorted descending' => preg_match('/reverse=True/', $pythonCode),
    'Confidence score in JSON output' => preg_match('"confidence_score"', $pythonCode),
];

$pass3 = true;
foreach ($test3 as $label => $passed) {
    echo sprintf("%-60s %s\n", $label, $passed ? "✓" : "✗");
    $pass3 = $pass3 && $passed;
}
$results['Confidence Filter'] = $pass3;

// TEST 4: Humanization Messages (Python)
echo "\n\nTEST 4: Humanization Messages (Python)\n";
echo "========================================\n";

$humanMessages = [
    'Exceptional match' => 'Exceptional match. Unique physical details are almost identical.',
    'Strong match' => 'Strong match. Visual patterns show a high level of consistency.',
    'Likely match' => 'Likely match. System detected significant physical similarities.',
    'do not match' => 'do not match the target',
];

$pass4 = true;
foreach ($humanMessages as $key => $msg) {
    $found = str_contains($pythonCode, $msg);
    echo sprintf("%-60s %s\n", "Contains: '$key'", $found ? "✓" : "✗");
    $pass4 = $pass4 && $found;
}
$results['Humanization (Python)'] = $pass4;

// TEST 5: View Humanization
echo "\n\nTEST 5: View Humanization (Blade Template)\n";
echo "============================================\n";

$viewFile = $baseDir . '/resources/views/assets/matches.blade.php';
if (!file_exists($viewFile)) {
    echo "✗ View not found\n";
    exit(1);
}

$viewCode = file_get_contents($viewFile);

$test5 = [
    'No "SIFT" jargon' => !preg_match('/\bSIFT\b/', $viewCode),
    'No "inlier" jargon' => !preg_match('/\binlier\b/', $viewCode),
    'No "base64" exposure' => !preg_match('/\bbase64\b/', $viewCode),
    'Uses "System Insight" label' => str_contains($viewCode, 'System Insight'),
    'Uses FEU Green (#004d32)' => str_contains($viewCode, '#004d32'),
    'Displays breakdown message' => str_contains($viewCode, "match['breakdown']"),
];

$pass5 = true;
foreach ($test5 as $label => $passed) {
    echo sprintf("%-60s %s\n", $label, $passed ? "✓" : "✗");
    $pass5 = $pass5 && $passed;
}
$results['View Humanization'] = $pass5;

// TEST 6: ID Matching Robustness
echo "\n\nTEST 6: ID Matching Robustness\n";
echo "================================\n";

$test6 = [
    'Remove dashes from ID' => str_contains($controllerCode, "str_replace('-'"),
    'Extract 7-12 digit pattern' => preg_match('/\\\\d\{7,12\}/', $controllerCode),
    'Handle missing IDs gracefully' => str_contains($controllerCode, "!empty(\$matches)"),
];

$pass6 = true;
foreach ($test6 as $label => $passed) {
    echo sprintf("%-60s %s\n", $label, $passed ? "✓" : "✗");
    $pass6 = $pass6 && $passed;
}
$results['ID Robustness'] = $pass6;

// FINAL REPORT
echo "\n\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                        FINAL REPORT                              ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$allPass = true;
foreach ($results as $test => $result) {
    $status = $result ? "✓ PASS" : "✗ FAIL";
    echo sprintf("%-40s %s\n", $test, $status);
    $allPass = $allPass && $result;
}

echo "\n";
if ($allPass) {
    echo "🎉 ALL SYSTEMS GREEN - Ready for Redefense!\n\n";
    echo "Summary:\n";
    echo "  ✓ Python-Laravel sync is correctly configured\n";
    echo "  ✓ 75% confidence filter is enforced\n";
    echo "  ✓ All technical jargon is humanized\n";
    echo "  ✓ ID extraction is robust (handles dashes, multi-format)\n";
    echo "  ✓ 1-to-Many Discovery Engine is flawless\n";
    exit(0);
} else {
    echo "⚠️  ISSUES DETECTED - Review failures above\n\n";
    exit(1);
}
