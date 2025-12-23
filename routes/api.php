<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API Routes (prefix /api)
Route::prefix('api')->group(function () {
    // Main API Routes for Dashboard Frontend
    Route::prefix('admin')->group(function () {
        // Dashboard data endpoint
        Route::get('/dashboard-data', [AdminController::class, 'getDashboardData']);
        
        // Setoran management
        Route::post('/setoran', [AdminController::class, 'storeSetoran']);
        
        // Laporan generation
        Route::post('/laporan', [AdminController::class, 'generateLaporan']);
        
        // Nasabah management
        Route::get('/nasabah', [AdminController::class, 'getNasabah']);
        Route::get('/search-nasabah', [AdminController::class, 'searchNasabah']);
        Route::post('/nasabah', [AdminController::class, 'createNasabah']);
        Route::put('/nasabah/{id}', [AdminController::class, 'updateNasabah']);
        Route::delete('/nasabah/{id}', [AdminController::class, 'deleteNasabah']);
        
        // Export Laporan Routes (Konsolidasi: download route dipindah ke web.php untuk consistency)
        Route::prefix('export')->group(function () {
            Route::post('/excel', [ExportController::class, 'exportExcel'])->name('admin.export.excel');
            Route::post('/pdf', [ExportController::class, 'exportPDF'])->name('admin.export.pdf');
            // Note: Download route dipindah ke web.php sebagai /admin/export/download/{filename}
        });
    });
    
    // Direct API routes (alternative endpoints for frontend flexibility)
    Route::get('/dashboard-data', [AdminController::class, 'getDashboardData']);
    Route::get('/search-nasabah', [AdminController::class, 'searchNasabah']);
    Route::get('/nasabah', [AdminController::class, 'getNasabah']);
    
    // Setoran API - Uses the newer storeSetoranApi method for comprehensive validation
    Route::post('/setoran', [AdminController::class, 'storeSetoranApi']);
    
    // Test endpoint untuk debugging
    Route::get('/test-export', function() {
        return response()->json([
            'status' => 'ok',
            'message' => 'Export API is working',
            'timestamp' => now()->toISOString(),
            'php_version' => PHP_VERSION,
            'storage_path' => storage_path('app/exports'),
            'storage_exists' => is_dir(storage_path('app/exports')),
            'storage_writable' => is_writable(storage_path('app/exports'))
        ]);
    });
});
