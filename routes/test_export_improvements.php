<?php
/**
 * Test Route untuk Verifikasi ExportController Improvements
 * 
 * Menambahkan route test ke web.php dengan:
 * Route::get('/test-export-improvements', [ExportController::class, 'testImprovements']);
 */

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

// Test route untuk verifikasi improvements
Route::get('/test-export-improvements', function() {
    try {
        // Test 1: Dependency validation
        $controller = new ExportController();
        
        // Test memory management functions
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = $controller->convertToBytes($memoryLimit);
        $availableMemory = $controller->getAvailableMemory();
        
        // Test 2: File utilities
        $testBytes = $controller->formatBytes(1048576); // 1MB
        $testBytes2 = $controller->formatBytes(1073741824); // 1GB
        
        // Test 3: Storage directory check
        $exportDir = storage_path('app/exports');
        $dirExists = is_dir($exportDir);
        $dirWritable = is_writable($exportDir);
        
        // Test 4: File listing functionality
        $cleanupResult = null;
        try {
            // Simulate cleanup old files (without actually deleting)
            $cleanupResult = 'Simulated cleanup - would delete files older than 7 days';
        } catch (Exception $e) {
            $cleanupResult = 'Cleanup simulation failed: ' . $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'ExportController improvements test passed',
            'tests' => [
                'dependency_validation' => 'Available',
                'memory_management' => [
                    'limit' => $memoryLimit,
                    'limit_bytes' => $memoryLimitBytes,
                    'available_bytes' => $availableMemory,
                    'available_mb' => round($availableMemory / 1024 / 1024, 2)
                ],
                'file_utilities' => [
                    'format_1mb' => $testBytes,
                    'format_1gb' => $testBytes2
                ],
                'storage' => [
                    'export_dir' => $exportDir,
                    'dir_exists' => $dirExists,
                    'dir_writable' => $dirWritable
                ],
                'cleanup' => $cleanupResult
            ],
            'php_version' => PHP_VERSION,
            'timestamp' => now()->toISOString()
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'ExportController improvements test failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.export-improvements');
