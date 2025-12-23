<?php


use App\Models\User;
use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================
// Halaman Landing
// ========================
Route::get('/', function () {
    return view('landing');
});

// ========================
// Register Nasabah
// ========================
Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'nama_lengkap' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'string', 'min:8'],
        'no_hp' => ['required', 'string', 'max:50'],
        'alamat' => ['required', 'string'],
    ]);

    User::create([
        'name' => $validated['nama_lengkap'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'nasabah',
        'no_hp' => $validated['no_hp'],
        'alamat' => $validated['alamat'],
        'saldo' => 0,
    ]);

    return redirect('/login')->with('status', 'Registrasi berhasil, silakan masuk.');
});

// ========================
// Login Nasabah & Pengelola
// ========================
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::get('/login/pengelola', function () {
    return view('login', ['role' => 'pengelola']);
})->name('login.pengelola');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        'role' => ['nullable', 'in:nasabah,pengelola'],
    ]);

    $role = $credentials['role'] ?? 'nasabah';
    unset($credentials['role']); // hapus role dari credentials

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($role === 'pengelola' && $user->role !== 'pengelola') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun ini bukan akun pengelola.',
            ])->onlyInput('email');
        }

        // Redirect berdasarkan role
        if ($user->role === 'pengelola') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('nasabah.dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
});


// ========================
// Dashboard Admin Dinamis
// ========================
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// Dashboard Nasabah
Route::middleware('auth')->get('/nasabah/dashboard', function () {
    return view('nasabah.dashboard');
})->name('nasabah.dashboard');

// Profil Nasabah
Route::middleware('auth')->get('/nasabah/profile', function () {
    return view('nasabah.profile');
})->name('nasabah.profile');

// ========================
// Form Input Setoran (Pengelola) - VERSI BARU
// ========================
Route::get('/pengelola/input-setoran-new', function () {
    return view('admin.input-setoran-new');
})->name('pengelola.input-setoran-new');

Route::get('/pengelola/input-setoran', function () {
    return view('admin.input-setoran');
})->name('pengelola.input-setoran');

// ========================
// Simpan Nasabah Baru & Setoran Detail - JSON Response
// ========================
Route::post('/pengelola/add-nasabah-setoran', function (Request $request) {
    try {
        // VALIDATION - Menghindari false positive errors
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_hp' => 'required|string|min:10|max:20',
            'alamat' => 'required|string',
            'items' => 'required|array|min:1|max:50'
        ], [
            'nama_lengkap.required' => '📝 Nama lengkap harus diisi',
            'email.required' => '📧 Alamat email harus diisi',
            'email.email' => '📧 Format email tidak valid',
            'email.unique' => '📧 Email sudah terdaftar',
            'no_hp.required' => '📱 Nomor HP harus diisi',
            'alamat.required' => '📍 Alamat harus diisi',
            'items.required' => '🗂️ Minimal satu jenis sampah harus ditambahkan'
        ]);

        DB::beginTransaction();
        
        // COMPREHENSIVE LOGGING untuk debugging
        \Log::info('🎯 JSON API - Starting add nasabah setoran process', [
            'email' => $validated['email'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'items_count' => count($validated['items']),
            'request_ip' => $request->ip(),
            'timestamp' => now()
        ]);

        // Data integrity check - pastikan semua item valid
        foreach ($validated['items'] as $index => $item) {
            if (!isset($item['jenis']) || empty(trim($item['jenis']))) {
                return response()->json([
                    'success' => false,
                    'message' => "Jenis sampah pada baris " . ($index + 1) . " tidak boleh kosong.",
                    'error_code' => 'INVALID_JENIS',
                    'field' => "items.{$index}.jenis"
                ], 422);
            }
            
            if (!isset($item['berat']) || !is_numeric($item['berat']) || $item['berat'] <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Berat sampah pada baris " . ($index + 1) . " harus berupa angka dan lebih dari 0.",
                    'error_code' => 'INVALID_BERAT',
                    'field' => "items.{$index}.berat"
                ], 422);
            }
            
            if (!isset($item['harga_per_kg']) || !is_numeric($item['harga_per_kg']) || $item['harga_per_kg'] < 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Harga per kg pada baris " . ($index + 1) . " harus berupa angka dan tidak boleh negatif.",
                    'error_code' => 'INVALID_HARGA',
                    'field' => "items.{$index}.harga_per_kg"
                ], 422);
            }
        }

        // Cek apakah nasabah sudah ada
        $nasabah = User::where('email', $validated['email'])->first();

        if (!$nasabah) {
            // Jika belum ada, buat nasabah baru
            $nasabah = User::create([
                'name' => trim($validated['nama_lengkap']),
                'email' => strtolower(trim($validated['email'])),
                'password' => bcrypt('password123'),
                'role' => 'nasabah',
                'no_hp' => trim($validated['no_hp']),
                'alamat' => trim($validated['alamat']),
                'saldo' => 0,
            ]);
            
            \Log::info('Created new nasabah', [
                'nasabah_id' => $nasabah->id,
                'email' => $nasabah->email
            ]);
        } else {
            \Log::info('Using existing nasabah', [
                'nasabah_id' => $nasabah->id,
                'email' => $nasabah->email,
                'current_saldo' => $nasabah->saldo
            ]);
        }

        // Calculate totals dari items dengan presisi
        $totalJumlah = 0;
        $totalBerat = 0;
        $processedItems = [];

        foreach ($validated['items'] as $item) {
            $berat = (float) $item['berat'];
            $hargaPerKg = (float) $item['harga_per_kg'];
            $subtotal = $berat * $hargaPerKg;
            
            $totalJumlah += $subtotal;
            $totalBerat += $berat;
            
            $processedItems[] = [
                'jenis' => trim($item['jenis']),
                'berat' => $berat,
                'harga_per_kg' => $hargaPerKg,
                'subtotal' => $subtotal
            ];
        }

        // Validasi total sebelum menyimpan
        if ($totalJumlah <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total nilai setoran harus lebih dari 0.',
                'error_code' => 'INVALID_TOTAL_JUMLAH'
            ], 422);
        }

        if ($totalBerat <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total berat setoran harus lebih dari 0.',
                'error_code' => 'INVALID_TOTAL_BERAT'
            ], 422);
        }

        // Tambah setoran baru dengan detail items
        $setoran = $nasabah->setoran()->create([
            'jumlah' => round($totalJumlah, 2),
            'total_berat' => round($totalBerat, 2),
            'keterangan' => 'Setoran oleh pengelola - ' . now()->format('d/m/Y H:i'),
            'items' => json_encode($processedItems, JSON_UNESCAPED_UNICODE),
        ]);

        // Update saldo dengan transaction yang aman
        $nasabah->increment('saldo', round($totalJumlah, 2));

        DB::commit();

        \Log::info('Successfully added nasabah setoran', [
            'nasabah_id' => $nasabah->id,
            'setoran_id' => $setoran->id,
            'total_jumlah' => $totalJumlah,
            'total_berat' => $totalBerat,
            'new_saldo' => $nasabah->fresh()->saldo
        ]);

        // SELALU RETURN JSON - TIDAK ADA REDIRECT
        return response()->json([
            'success' => true,
            'message' => 'Setoran berhasil disimpan!',
            'data' => [
                'nasabah' => [
                    'id' => $nasabah->id,
                    'nama' => $nasabah->name,
                    'email' => $nasabah->email,
                    'saldo_baru' => $nasabah->fresh()->saldo
                ],
                'setoran' => [
                    'id' => $setoran->id,
                    'total_jumlah' => $totalJumlah,
                    'total_berat' => $totalBerat,
                    'jumlah_item' => count($processedItems),
                    'keterangan' => $setoran->keterangan
                ],
                'items' => $processedItems,
                'timestamp' => now()->toISOString()
            ]
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollback();
        \Log::error('Database error adding nasabah setoran', [
            'error' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan database saat menyimpan data. Silakan coba lagi.',
            'error_code' => 'DATABASE_ERROR',
            'debug' => config('app.debug') ? $e->getMessage() : null
        ], 500);
            
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollback();
        \Log::error('Validation error adding nasabah setoran', [
            'errors' => $e->errors(),
            'request_data' => $request->except(['_token'])
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Data yang dikirim tidak valid.',
            'errors' => $e->errors(),
            'error_code' => 'VALIDATION_ERROR'
        ], 422);
            
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Error adding nasabah setoran', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->except(['_token'])
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
            'error_code' => 'GENERAL_ERROR',
            'debug' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
})->name('pengelola.add-nasabah-setoran');



// ========================
// API Routes untuk Dashboard Dinamis
// ========================
Route::get('/api/admin/dashboard-data', [AdminController::class, 'getDashboardData']);


// API untuk mendapatkan daftar nasabah
Route::get('/api/admin/nasabah', [AdminController::class, 'getNasabah']);

// API untuk menambah setoran
Route::post('/api/admin/setoran', [AdminController::class, 'storeSetoran']);

// API untuk generate laporan
Route::post('/api/admin/laporan', [AdminController::class, 'generateLaporan']);

// Export routes - Konsolidasi dengan API routes
// Excel/PDF generation: POST /api/admin/export/excel dan POST /api/admin/export/pdf (via API)
// File download: GET /admin/export/download/{filename} (direct web access)

// API untuk kelola nasabah
Route::post('/api/admin/nasabah', [AdminController::class, 'createNasabah']);
Route::put('/api/admin/nasabah/{id}', [AdminController::class, 'updateNasabah']);
Route::delete('/api/admin/nasabah/{id}', [AdminController::class, 'deleteNasabah']);
Route::get('/api/admin/search-nasabah', [AdminController::class, 'searchNasabah']);

// API untuk nasabah dashboard data
Route::middleware('auth')->get('/api/nasabah/dashboard-data', [NasabahController::class, 'getDashboardData']);

// API untuk riwayat setoran nasabah
Route::middleware('auth')->get('/api/nasabah/setoran-history', [NasabahController::class, 'getSetoranHistory']);

// API untuk data profil nasabah
Route::middleware('auth')->get('/api/nasabah/profile-data', [NasabahController::class, 'getProfileData']);

// ========================
// Export Laporan Routes (WEB - Direct Download Access)
// ========================

// Download file export yang sudah digenerate (WEB route untuk direct access dengan authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/export/download/{filename}', [ExportController::class, 'downloadFile'])->name('admin.export.download');
});

// Note: Export generation routes dipindah ke API untuk konsistensi dan security
// Access via: POST /api/admin/export/excel dan POST /api/admin/export/pdf



// ========================
// Logout Routes
// ========================
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('status', 'Anda telah logout.');
})->name('logout');

// ========================
// Route Tes Setoran (opsional)
// ========================
Route::get('/tes-setoran', function () {
    $user = User::first(); 

    if (!$user) {
        return 'Belum ada user. Silakan register dulu.';
    }

    $setoran = $user->setoran()->create([
        'jumlah' => 50000,
        'keterangan' => 'Setoran percobaan'
    ]);

    $user->saldo = $user->setoran()->sum('jumlah');
    $user->save();

    return [
        'User' => $user->name,
        'Saldo' => $user->saldo,
        'Setoran Terakhir' => $setoran->jumlah
    ];
});

// ========================
// TEST ROUTES (tanpa authentication)
// ========================
Route::get('/test/input-setoran', [TestController::class, 'testInputSetoran']);
Route::post('/test/process-input', [TestController::class, 'processTestInput']);
Route::get('/test/page', [TestController::class, 'testPage']);
Route::get('/test/database', [TestController::class, 'testDatabase']);

// ========================
// ROUTE TEST TANPA VALIDATION (untuk isolate masalah)
// ========================
Route::post('/test/no-validation', function (Request $request) {
    try {
        // Log semua data yang diterima
        \Log::info('TEST NO VALIDATION - Request received', [
            'all_data' => $request->all(),
            'has_items' => isset($request->items),
            'items_count' => isset($request->items) ? count($request->items) : 0,
            'timestamp' => now()
        ]);

        // TIDAK ADA VALIDATION SAMA SEKALI
        // Langsung proses data
        
        $data = [
            'status' => 'SUCCESS',
            'message' => 'Data berhasil diterima tanpa validation!',
            'received_data' => $request->all(),
            'timestamp' => now()->toDateTimeString()
        ];

        \Log::info('TEST NO VALIDATION - Success', $data);

        return response()->json($data);

    } catch (\Exception $e) {
        \Log::error('TEST NO VALIDATION - Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'ERROR',
            'message' => 'Terjadi error: ' . $e->getMessage(),
            'timestamp' => now()->toDateTimeString()
        ], 500);
    }
})->name('test.no-validation');

// ========================
// TEST EXPORT STORAGE ROUTE
// ========================
Route::get('/test-export', function () {
    try {
        $storagePath = storage_path('app/public');
        $exists = file_exists($storagePath);
        $writable = is_writable($storagePath);
        
        return response()->json([
            'storage_path' => $storagePath,
            'storage_exists' => $exists,
            'storage_writable' => $writable,
            'php_version' => PHP_VERSION,
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('test.export');
