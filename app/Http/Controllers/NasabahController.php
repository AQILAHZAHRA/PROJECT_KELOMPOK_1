<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setoran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NasabahController extends Controller
{
    /**
     * Get dashboard data for nasabah
     */
    public function getDashboardData(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Total saldo nasabah
            $totalSaldo = $user->saldo;

            // Total setoran nasabah
            $totalSetoran = $user->setoran()->sum('jumlah');

            // Total berat sampah nasabah
            $totalBerat = $user->setoran()->sum('total_berat') ?? 0;

            // Setoran bulan ini
            $setoranBulanIni = $user->setoran()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah');

            // Setoran terbaru (5 terakhir)
            $setoranTerbaru = $user->setoran()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($setoran) {
                    return [
                        'id' => $setoran->id,
                        'jumlah' => $setoran->jumlah,
                        'total_berat' => $setoran->total_berat ?? 0,
                        'keterangan' => $setoran->keterangan ?? 'Setoran sampah',
                        'tanggal' => $setoran->created_at->timezone('Asia/Makassar')->format('d/m/Y'),
                        'waktu' => $setoran->created_at->timezone('Asia/Makassar')->format('H:i'),
                        'created_at' => $setoran->created_at
                    ];
                });

            $data = [
                'totalSaldo' => (int) $totalSaldo,
                'totalSetoran' => (int) $totalSetoran,
                'totalBerat' => (float) $totalBerat,
                'setoranBulanIni' => (int) $setoranBulanIni,
                'setoranTerbaru' => $setoranTerbaru,
                'lastUpdated' => now()->timestamp
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Dashboard data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching nasabah dashboard data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get setoran history for nasabah
     */
    public function getSetoranHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $setoran = $user->setoran()
                ->orderBy('created_at', 'desc')
                ->paginate(10); // Pagination untuk history

            $data = $setoran->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->created_at->timezone('Asia/Makassar')->format('d/m/Y'),
                    'waktu' => $item->created_at->timezone('Asia/Makassar')->format('H:i') . ' WITA',
                    'jumlah' => number_format($item->jumlah, 0, ',', '.'),
                    'total_berat' => number_format($item->total_berat ?? 0, 2, ',', '.'),
                    'keterangan' => $item->keterangan ?? 'Setoran sampah'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $setoran->currentPage(),
                    'last_page' => $setoran->lastPage(),
                    'per_page' => $setoran->perPage(),
                    'total' => $setoran->total()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching setoran history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch setoran history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get profile data for nasabah
     */
    public function getProfileData(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get statistics
            $totalSetoran = $user->setoran()->sum('jumlah');
            $totalBerat = $user->setoran()->sum('total_berat') ?? 0;
            $jumlahTransaksi = $user->setoran()->count();

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'alamat' => $user->alamat,
                'saldo' => (int) $user->saldo,
                'total_setoran' => (int) $totalSetoran,
                'total_berat' => (float) $totalBerat,
                'jumlah_transaksi' => (int) $jumlahTransaksi,
                'created_at_formatted' => $user->created_at->format('d F Y'),
                'role' => $user->role
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Profile data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching nasabah profile data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
