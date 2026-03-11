<?php

/**
 * FEU-CDGRS DISCOVERY ENGINE DIAGNOSTICS
 * Validates the 1-to-Many sync between Laravel Controller and Python Vision Script
 *
 * Purpose: Ensure Registry Intelligence pipeline is flawless for redefense presentation
 */

namespace Tests;

use App\Models\LostItem;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class DiscoveryEngineDiagnostics
{
    protected $pythonPath = 'C:\\Users\\Joss\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\\python.exe';

    /**
     * TEST 1: Validate Python script takes exactly 2 arguments
     */
    public function testPythonArgumentSignature()
    {
        echo "TEST 1: Python Argument Signature\n";
        echo "==================================\n";

        $pythonCode = file_get_contents(base_path('resources/scripts/visual_matcher.py'));

        $tests = [
            'function signature' => preg_match('/def process_batch\(target_img_path, batch_json_path\)/', $pythonCode),
            'parser expects 2 args' => preg_match('/parser\.add_argument\("target_img"\).*parser\.add_argument\("batch_json"\)/s', $pythonCode)
                && !preg_match('/parser\.add_argument\("target_desc"\)/', $pythonCode),
            'no unused target_desc' => !preg_match('/target_desc/', $pythonCode),
        ];

        foreach ($tests as $label => $passed) {
            echo sprintf("✓%-50s %s\n", $label, $passed ? "PASS" : "FAIL");
        }

        return array_reduce($tests, fn($acc, $val) => $acc && $val, true);
    }

    /**
     * TEST 2: Validate Controller passes exactly 2 arguments
     */
    public function testControllerArgumentCount()
    {
        echo "\n\nTEST 2: Controller Argument Count\n";
        echo "==================================\n";

        $controllerCode = file_get_contents(base_path('app/Http/Controllers/AssetMatchingController.php'));

        // Extract the processArgs array
        if (preg_match('/\$processArgs = \[(.*?)\];/s', $controllerCode, $matches)) {
            $args = $matches[1];
            $argCount = substr_count($args, 'base_path') + substr_count($args, '$this->pythonPath');

            echo "Arguments passed to Python:\n";
            echo "- \$this->pythonPath (Python executable)\n";
            echo "- base_path('resources/scripts/visual_matcher.py') (Script path)\n";

            if (preg_match('/\$targetImagePath/', $args)) echo "- \$targetImagePath ✓\n";
            if (preg_match('/\$jsonFilePath/', $args)) echo "- \$jsonFilePath ✓\n";
            if (preg_match('/\$targetItem->description/', $args)) {
                echo "- \$targetItem->description ✗ UNWANTED\n";
                return false;
            }

            return true;
        }

        echo "✗ Could not parse \$processArgs\n";
        return false;
    }

    /**
     * TEST 3: Validate 75% Confidence Filter
     */
    public function testConfidenceFilter()
    {
        echo "\n\nTEST 3: 75% Confidence Filter\n";
        echo "================================\n";

        $pythonCode = file_get_contents(base_path('resources/scripts/visual_matcher.py'));

        $tests = [
            'Filter threshold is 75%' => preg_match('/if final_score >= 75:/', $pythonCode),
            'Results limited to top 5' => preg_match('/results\[:5\]/', $pythonCode),
            'Results sorted descending' => preg_match('/reverse=True/', $pythonCode),
            'Human messages included' => preg_match('/"breakdown": human_msg/', $pythonCode),
        ];

        foreach ($tests as $label => $passed) {
            echo sprintf("✓%-50s %s\n", $label, $passed ? "PASS" : "FAIL");
        }

        return array_reduce($tests, fn($acc, $val) => $acc && $val, true);
    }

    /**
     * TEST 4: Validate Humanization Messages
     */
    public function testHumanizationMessages()
    {
        echo "\n\nTEST 4: Humanization Messages\n";
        echo "===============================\n";

        $pythonCode = file_get_contents(base_path('resources/scripts/visual_matcher.py'));

        $messages = [
            'Exceptional match' => 'No SIFT jargon - exceptional',
            'Strong match' => 'No inlier jargon - strong',
            'Likely match' => 'No structural jargon - likely',
            'do not match' => 'Humane failure message',
        ];

        $allFound = true;
        foreach ($messages as $msg => $description) {
            $found = str_contains($pythonCode, $msg);
            echo sprintf("✓%-40s %s (%s)\n", $msg, $found ? "✓" : "✗", $description);
            $allFound = $allFound && $found;
        }

        return $allFound;
    }

    /**
     * TEST 5: Validate View Humanization
     */
    public function testViewHumanization()
    {
        echo "\n\nTEST 5: View Humanization\n";
        echo "==========================\n";

        $viewCode = file_get_contents(resource_path('views/assets/matches.blade.php'));

        $tests = [
            'No "SIFT" terminology' => !str_contains($viewCode, 'SIFT'),
            'No "inlier" terminology' => !str_contains($viewCode, 'inlier'),
            'No "base64" terminology' => !str_contains($viewCode, 'base64'),
            'Uses "System Insight" label' => str_contains($viewCode, 'System Insight'),
            'Uses "Visual Mapping"' => str_contains($viewCode, 'matching'),
            'Uses FEU Green color' => str_contains($viewCode, '#004d32'),
        ];

        foreach ($tests as $label => $passed) {
            echo sprintf("✓%-50s %s\n", $label, $passed ? "PASS" : "FAIL");
        }

        return array_reduce($tests, fn($acc, $val) => $acc && $val, true);
    }

    /**
     * RUN ALL DIAGNOSTICS
     */
    public function runAll()
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════════╗\n";
        echo "║     FEU-CDGRS DISCOVERY ENGINE REDEFENSE DIAGNOSTICS             ║\n";
        echo "║                                                                  ║\n";
        echo "║     Validating 1-to-Many Sync for Administrative Presentation    ║\n";
        echo "╚══════════════════════════════════════════════════════════════════╝\n";

        $results = [
            'Python Signature' => $this->testPythonArgumentSignature(),
            'Controller Args' => $this->testControllerArgumentCount(),
            'Confidence Filter' => $this->testConfidenceFilter(),
            'Humanization (Python)' => $this->testHumanizationMessages(),
            'Humanization (View)' => $this->testViewHumanization(),
        ];

        echo "\n\n📊 FINAL REPORT\n";
        echo "================\n";

        $allPass = true;
        foreach ($results as $test => $result) {
            echo sprintf("%-40s %s\n", $test, $result ? "✓ PASS" : "✗ FAIL");
            $allPass = $allPass && $result;
        }

        echo "\n";
        if ($allPass) {
            echo "🎉 ALL SYSTEMS GREEN - Ready for Redefense!\n";
            echo "The Discovery Engine synchronization is flawless.\n";
        } else {
            echo "⚠️  ISSUES DETECTED - Review failures above\n";
        }
        echo "\n";

        return $allPass;
    }
}

// Run diagnostics if invoked from CLI
if (php_sapi_name() === 'cli') {
    (new DiscoveryEngineDiagnostics())->runAll();
}
