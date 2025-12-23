<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Get dashboard data for admin panel
     */
    public function getDashboardData(): JsonResponse
    {
        try {
            // Get total number of customers (users with role 'nasabah')
            $totalNasabah = User::where('role', 'nasabah')->count();

            // Get total setoran (sum of all setoran amounts)
            $totalSetoran = Setoran::sum('jumlah');

            // Get total saldo overall (sum of all user saldo)
            $totalSaldo = User::where('role', 'nasabah')->sum('saldo');

            // Get setoran for current month
            $setoranBulanIni = Setoran::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah');

            // Get latest setoran (last 5 transactions)
            $setoranTerbaru = Setoran::with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($setoran) {
                    return [
                        'id' => $setoran->id,
                        'nama' => $setoran->user->name ?? 'Unknown',
                        'jumlah' => $setoran->jumlah,
                        'keterangan' => $setoran->keterangan ?? 'Setoran sampah',
                        'tanggal' => $setoran->created_at->timezone('Asia/Makassar')->format('d/m/Y'),
                        'waktu' => $setoran->created_at->timezone('Asia/Makassar')->format('H:i'),
                        'created_at' => $setoran->created_at
                    ];
                });

            $data = [
                'totalNasabah' => (int) $totalNasabah,
                'totalSetoran' => (int) $totalSetoran,
                'totalSaldo' => (int) $totalSaldo,
                'setoranBulanIni' => (int) $setoranBulanIni,
                'setoranTerbaru' => $setoranTerbaru
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Dashboard data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching dashboard data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for initial page load (for server-side rendering)
     */
    public function dashboard()
    {
        try {
            // Get total number of customers (users with role 'nasabah')
            $totalNasabah = User::where('role', 'nasabah')->count();

            // Get total setoran (sum of all setoran amounts)
            $totalSetoran = Setoran::sum('jumlah');

            // Get total saldo overall (sum of all user saldo)
            $totalSaldo = User::where('role', 'nasabah')->sum('saldo');

            // Get setoran for current month
            $setoranBulanIni = Setoran::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah');

            // Get latest setoran (last 10 transactions)
            $setoranTerbaru = Setoran::with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('admin.dashboard', compact(
                'totalNasabah',
                'totalSetoran', 
                'totalSaldo',
                'setoranBulanIni',
                'setoranTerbaru'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading dashboard: ' . $e->getMessage());
            
            // Return with default values if there's an error
            return view('admin.dashboard', [
                'totalNasabah' => 0,
                'totalSetoran' => 0,
                'totalSaldo' => 0,
                'setoranBulanIni' => 0,
                'setoranTerbaru' => collect()
            ]);
        }
    }

    /**
     * Store a new setoran transaction
     */
    public function storeSetoran(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.jenis' => 'required|string',
            'items.*.berat' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric|min:0',
            'total_berat' => 'required|numeric|min:0',
            'total_nilai' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Validate that totals match calculated values
            $calculatedBerat = collect($request->items)->sum('berat');
            $calculatedNilai = collect($request->items)->sum(function($item) {
                return $item['berat'] * $item['harga'];
            });

            if (abs($calculatedBerat - $request->total_berat) > 0.01) {
                throw new \Exception('Total berat tidak sesuai');
            }

            if (abs($calculatedNilai - $request->total_nilai) > 1) {
                throw new \Exception('Total nilai tidak sesuai');
            }

            // Create setoran record
            $setoran = Setoran::create([
                'user_id' => $request->user_id,
                'jumlah' => $request->total_nilai,
                'total_berat' => $request->total_berat,
                'keterangan' => 'Setoran sampah',
                'items' => json_encode($request->items),
                'created_at' => now()
            ]);

            // Update user's saldo
            $user = User::find($request->user_id);
            $user->increment('saldo', $request->total_nilai);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Setoran berhasil dicatat',
                'data' => [
                    'setoran_id' => $setoran->id,
                    'total_jumlah' => $request->total_nilai,
                    'total_berat' => $request->total_berat,
                    'new_saldo' => $user->fresh()->saldo,
                    'user_name' => $user->name,
                    'timestamp' => now()->timestamp,
                    'items' => $request->items
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error storing setoran: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setoran: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get list of customers for dropdown
     */
    public function getNasabah(): JsonResponse
    {
        try {
            $customers = User::where('role', 'nasabah')
                ->select('id', 'name', 'saldo')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate laporan setoran
     */
    public function generateLaporan(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai'
        ]);

        try {
            $setoran = Setoran::with('user:id,name')
                ->whereBetween('created_at', [
                    $request->tanggal_mulai . ' 00:00:00',
                    $request->tanggal_akhir . ' 23:59:59'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            $totalBerat = $setoran->sum('total_berat');
            $totalNilai = $setoran->sum('jumlah');

            $data = $setoran->map(function ($item, $index) {
                // Decode items if they exist
                $items = [];
                if ($item->items) {
                    $items = json_decode($item->items, true);
                    $jenisItems = array_column($items, 'jenis');
                    $jenisString = implode(', ', $jenisItems);
                } else {
                    $jenisString = 'Sampah';
                }

                return [
                    'no' => $index + 1,
                    'tanggal' => $item->created_at->timezone('Asia/Makassar')->format('d/m/Y'),
                    'waktu' => $item->created_at->timezone('Asia/Makassar')->format('H:i') . ' WITA',
                    'nama_nasabah' => $item->user->name ?? 'Unknown',
                    'jenis' => $jenisString,
                    'total_berat' => number_format($item->total_berat ?? 0, 2),
                    'total_nilai' => number_format($item->jumlah, 0, ',', '.')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'summary' => [
                    'total_berat' => number_format($totalBerat, 2),
                    'total_nilai' => number_format($totalNilai, 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating laporan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate laporan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new nasabah
     */
    public function createNasabah(Request $request): JsonResponse
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_hp' => 'required|string|max:50',
            'alamat' => 'required|string',
            'password' => 'nullable|string|min:6'
        ]);

        try {
            $password = $request->password ?: 'password123';

            $nasabah = User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => 'nasabah',
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'saldo' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nasabah berhasil ditambahkan',
                'data' => [
                    'id' => $nasabah->id,
                    'name' => $nasabah->name,
                    'email' => $nasabah->email,
                    'no_hp' => $nasabah->no_hp,
                    'alamat' => $nasabah->alamat,
                    'saldo' => $nasabah->saldo
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating nasabah: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah nasabah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing nasabah
     */
    public function updateNasabah(Request $request, $id): JsonResponse
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'no_hp' => 'required|string|max:50',
            'alamat' => 'required|string',
            'password' => 'nullable|string|min:6'
        ]);

        try {
            $nasabah = User::where('role', 'nasabah')->findOrFail($id);

            $updateData = [
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
            ];

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $nasabah->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data nasabah berhasil diperbarui',
                'data' => [
                    'id' => $nasabah->id,
                    'name' => $nasabah->name,
                    'email' => $nasabah->email,
                    'no_hp' => $nasabah->no_hp,
                    'alamat' => $nasabah->alamat,
                    'saldo' => $nasabah->saldo
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating nasabah: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data nasabah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete nasabah
     */
    public function deleteNasabah($id): JsonResponse
    {
        try {
            $nasabah = User::where('role', 'nasabah')->findOrFail($id);

            // Check if nasabah has setoran records
            $setoranCount = Setoran::where('user_id', $id)->count();
            
            if ($setoranCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus nasabah yang memiliki riwayat setoran'
                ], 400);
            }

            $nasabah->delete();

            return response()->json([
                'success' => true,
                'message' => 'Nasabah berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting nasabah: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus nasabah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Store setoran - JSON Response Only
     * CRITICAL FIX: Implementasi untuk memperbaiki saldo yang tidak terupdate
     */
    public function storeSetoranApi(Request $request): JsonResponse
    {
        try {
            \Log::info('🚀 API SETORAN DIPANGGIL', [
                'request_data' => $request->all(),
                'timestamp' => now()
            ]);

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
            \Log::info('🎯 API - Starting add nasabah setoran process', [
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

            // 🔥 CRITICAL FIX: Update saldo dengan transaction yang aman
            $nasabah->increment('saldo', round($totalJumlah, 2));
            $saldoBaru = $nasabah->fresh()->saldo;

            DB::commit();

            \Log::info('✅ Successfully added nasabah setoran via API', [
                'nasabah_id' => $nasabah->id,
                'setoran_id' => $setoran->id,
                'total_jumlah' => $totalJumlah,
                'total_berat' => $totalBerat,
                'saldo_lama' => $nasabah->saldo - round($totalJumlah, 2),
                'saldo_baru' => $saldoBaru
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
                        'saldo_baru' => $saldoBaru
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
            \Log::error('❌ Database error adding nasabah setoran via API', [
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
            \Log::error('❌ Validation error adding nasabah setoran via API', [
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
            \Log::error('❌ Error adding nasabah setoran via API', [
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
    }

    /**
     * Search nasabah
     */
    public function searchNasabah(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string|max:255'
        ]);

        try {
            $query = $request->query('query', '');

            $nasabah = User::where('role', 'nasabah')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($subQ) use ($query) {
                        $subQ->where('name', 'like', '%' . $query . '%')
                             ->orWhere('email', 'like', '%' . $query . '%')
                             ->orWhere('no_hp', 'like', '%' . $query . '%')
                             ->orWhere('alamat', 'like', '%' . $query . '%');
                    });
                })
                ->select('id', 'name', 'email', 'no_hp', 'alamat', 'saldo')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $nasabah,
                'total' => $nasabah->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error searching nasabah: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari nasabah',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
