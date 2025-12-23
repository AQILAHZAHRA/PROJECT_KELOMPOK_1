<?php

namespace App\Http\Controllers;

use App\Models\Setoran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends Controller
{
    /**
     * Validate dependencies and system requirements
     */
    private function validateDependencies(string $type): void
    {
        $requestId = uniqid('dep_check_');
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '8.0', '<')) {
            Log::error("[{$requestId}] PHP version too old", [
                'current_version' => PHP_VERSION,
                'required_version' => '8.0+'
            ]);
            throw new \Exception('PHP version 8.0 or higher is required');
        }

        // Check memory limit
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = $this->convertToBytes($memoryLimit);
        
        if ($memoryLimitBytes < 128 * 1024 * 1024) { // 128MB
            Log::warning("[{$requestId}] Low memory limit", [
                'current_limit' => $memoryLimit,
                'recommended' => '256M'
            ]);
        }

        // Check available memory
        $freeMemory = $this->getAvailableMemory();
        if ($freeMemory < 64 * 1024 * 1024) { // 64MB free
            Log::warning("[{$requestId}] Low available memory", [
                'free_memory_mb' => round($freeMemory / 1024 / 1024, 2)
            ]);
        }

        if ($type === 'excel') {
            // Check PhpSpreadsheet dependency
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                Log::error("[{$requestId}] PhpSpreadsheet not installed");
                throw new \Exception('PhpSpreadsheet library is required but not installed. Please run: composer require phpoffice/phpspreadsheet');
            }

            // Check if zip extension is available (required for Excel)
            if (!extension_loaded('zip')) {
                Log::error("[{$requestId}] ZIP extension not loaded");
                throw new \Exception('ZIP extension is required for Excel export but not loaded');
            }

            // Check if xml extension is available (required for Excel)
            if (!extension_loaded('xml')) {
                Log::error("[{$requestId}] XML extension not loaded");
                throw new \Exception('XML extension is required for Excel export but not loaded');
            }

        } elseif ($type === 'pdf') {
            // Check DomPDF dependency
            if (!class_exists('\Dompdf\Dompdf')) {
                Log::error("[{$requestId}] DomPDF not installed");
                throw new \Exception('DomPDF library is required but not installed. Please run: composer require dompdf/dompdf');
            }

            // Check if mbstring extension is available
            if (!extension_loaded('mbstring')) {
                Log::error("[{$requestId}] mbstring extension not loaded");
                throw new \Exception('mbstring extension is required for PDF export but not loaded');
            }
        }

        // Check storage directory
        $exportDir = storage_path('app/exports');
        if (!is_dir($exportDir)) {
            if (!mkdir($exportDir, 0755, true)) {
                Log::error("[{$requestId}] Cannot create exports directory", [
                    'path' => $exportDir
                ]);
                throw new \Exception('Cannot create exports directory. Please check storage permissions.');
            }
        }

        if (!is_writable($exportDir)) {
            Log::error("[{$requestId}] Exports directory not writable", [
                'path' => $exportDir
            ]);
            throw new \Exception('Exports directory is not writable. Please check storage permissions.');
        }

        // Check disk space (at least 100MB free)
        $freeSpace = disk_free_space($exportDir);
        if ($freeSpace < 100 * 1024 * 1024) { // 100MB
            Log::warning("[{$requestId}] Low disk space", [
                'free_space_mb' => round($freeSpace / 1024 / 1024, 2)
            ]);
        }

        Log::info("[{$requestId}] Dependencies validation passed", [
            'type' => $type,
            'php_version' => PHP_VERSION,
            'memory_limit' => $memoryLimit,
            'free_memory_mb' => round($freeMemory / 1024 / 1024, 2),
            'free_disk_space_mb' => round($freeSpace / 1024 / 1024, 2)
        ]);
    }

    /**
     * Convert memory string to bytes
     */
    private function convertToBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;

        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Get available memory in bytes
     */
    private function getAvailableMemory(): int
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit == -1) {
            return 512 * 1024 * 1024; // Assume 512MB if unlimited
        }

        $memoryLimitBytes = $this->convertToBytes($memoryLimit);
        $currentUsage = memory_get_usage(true);
        
        return $memoryLimitBytes - $currentUsage;
    }

    /**
     * Check if dataset is too large for standard processing
     */
    private function isLargeDataset(int $recordCount): bool
    {
        return $recordCount > 1000; // Consider datasets with more than 1000 records as large
    }

    /**
     * Get optimal chunk size for large datasets based on available memory
     */
    private function getOptimalChunkSize(): int
    {
        $availableMemory = $this->getAvailableMemory();
        
        // Use 10% of available memory for chunk processing
        $targetMemory = $availableMemory * 0.1;
        
        // Estimate memory per record (rough calculation)
        $memoryPerRecord = 1024; // 1KB per record estimate
        
        $chunkSize = (int) floor($targetMemory / $memoryPerRecord);
        
        // Clamp between 100 and 1000 records
        return max(100, min($chunkSize, 1000));
    }

    /**
     * Process data in chunks to handle large datasets efficiently
     */
    private function processDataInChunks(string $tanggalMulai, string $tanggalAkhir, callable $callback): array
    {
        $requestId = uniqid('chunk_');
        
        try {
            Log::info("[{$requestId}] Starting chunked data processing", [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_akhir' => $tanggalAkhir,
                'available_memory_mb' => round($this->getAvailableMemory() / 1024 / 1024, 2)
            ]);

            // First, get total count to determine chunking strategy
            $totalCount = Setoran::whereBetween('created_at', [
                $tanggalMulai . ' 00:00:00',
                $tanggalAkhir . ' 23:59:59'
            ])->count();

            if (!$this->isLargeDataset($totalCount)) {
                // Small dataset, process normally
                $data = Setoran::with('user:id,name')
                    ->whereBetween('created_at', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalAkhir . ' 23:59:59'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return $callback($data);
            }

            // Large dataset, process in chunks
            Log::info("[{$requestId}] Large dataset detected, using chunked processing", [
                'total_records' => $totalCount,
                'chunk_size' => $this->getOptimalChunkSize()
            ]);

            $chunkSize = $this->getOptimalChunkSize();
            $allData = collect();
            $chunk = 0;

            do {
                $chunk++;
                Log::info("[{$requestId}] Processing chunk", [
                    'chunk' => $chunk,
                    'chunk_size' => $chunkSize
                ]);

                $chunkData = Setoran::with('user:id,name')
                    ->whereBetween('created_at', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalAkhir . ' 23:59:59'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->offset(($chunk - 1) * $chunkSize)
                    ->limit($chunkSize)
                    ->get();

                $allData = $allData->concat($chunkData->toArray());

                // Clear memory
                unset($chunkData);
                
                // Check if we need to free up memory
                if (($chunk % 10) === 0) {
                    gc_collect_cycles();
                }

                $processedCount = $chunk * $chunkSize;
                $hasMoreData = $processedCount < $totalCount;

                Log::info("[{$requestId}] Chunk processed", [
                    'chunk' => $chunk,
                    'processed_records' => min($processedCount, $totalCount),
                    'remaining_records' => max(0, $totalCount - $processedCount)
                ]);

            } while ($hasMoreData);

            // Convert back to collection with relationships
            $dataWithRelationships = Setoran::with('user:id,name')
                ->whereBetween('created_at', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalAkhir . ' 23:59:59'
                ])
                ->whereIn('id', $allData->pluck('id')->toArray())
                ->orderBy('created_at', 'desc')
                ->get();

            $result = $callback($dataWithRelationships);

            Log::info("[{$requestId}] Chunked processing completed", [
                'total_chunks' => $chunk,
                'total_records_processed' => $totalCount,
                'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2)
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error("[{$requestId}] Error in chunked processing", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Enhanced Excel export with chunked processing for large datasets
     */
    public function exportExcelLargeDataset(Request $request): JsonResponse
    {
        $requestId = uniqid('export_large_');
        
        try {
            Log::info("[{$requestId}] Large dataset Excel export started", [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data' => $request->all()
            ]);

            // Check dependencies and system requirements first
            $this->validateDependencies('excel');

            // Enhanced validasi input (same as regular export)
            $validated = $request->validate([
                'tanggal_mulai' => [
                    'required',
                    'date',
                    'before_or_equal:tanggal_akhir',
                    'date_format:Y-m-d',
                    'before_or_equal:' . date('Y-m-d')
                ],
                'tanggal_akhir' => [
                    'required',
                    'date',
                    'after_or_equal:tanggal_mulai',
                    'date_format:Y-m-d',
                    'before_or_equal:' . date('Y-m-d')
                ]
            ], [
                'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
                'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh setelah tanggal akhir',
                'tanggal_mulai.date_format' => 'Format tanggal harus YYYY-MM-DD',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari ini',
                'tanggal_akhir.required' => 'Tanggal akhir wajib diisi',
                'tanggal_akhir.date' => 'Format tanggal akhir tidak valid',
                'tanggal_akhir.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai',
                'tanggal_akhir.date_format' => 'Format tanggal harus YYYY-MM-DD',
                'tanggal_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari hari ini'
            ]);

            $tanggalMulai = $request->tanggal_mulai;
            $tanggalAkhir = $request->tanggal_akhir;

            // Get total record count first
            $totalCount = Setoran::whereBetween('created_at', [
                $tanggalMulai . ' 00:00:00',
                $tanggalAkhir . ' 23:59:59'
            ])->count();

            if ($totalCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data setoran pada rentang tanggal yang dipilih'
                ], 404);
            }

            // Generate filename
            $filename = $this->generateFilename('excel', $tanggalMulai, $tanggalAkhir);
            $filePath = storage_path('app/exports/' . $filename);

            // Ensure directory exists
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            // Process data using chunked approach
            $result = $this->processDataInChunks($tanggalMulai, $tanggalAkhir, function($setoran) use ($filePath, $tanggalMulai, $tanggalAkhir, $requestId) {
                
                // Create new spreadsheet
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Set header
                $this->setExcelHeader($sheet, $tanggalMulai, $tanggalAkhir);

                // Set data
                $row = 8;
                $totalBerat = 0;
                $totalNilai = 0;
                $processedCount = 0;

                foreach ($setoran as $index => $item) {
                    $processedCount++;
                    
                    // Progress logging for large datasets
                    if ($processedCount % 500 === 0) {
                        Log::info("[{$requestId}] Progress update", [
                            'processed_records' => $processedCount,
                            'total_records' => $setoran->count(),
                            'progress_percentage' => round(($processedCount / $setoran->count()) * 100, 2)
                        ]);
                    }

                    // Decode items if available
                    $items = [];
                    $jenisString = 'Sampah';
                    if ($item->items) {
                        $items = json_decode($item->items, true);
                        if ($items && is_array($items)) {
                            $jenisItems = array_column($items, 'jenis');
                            $jenisString = implode(', ', $jenisItems);
                        }
                    }

                    $sheet->setCellValue('A' . $row, $index + 1);
                    $sheet->setCellValue('B' . $row, $item->created_at->timezone('Asia/Makassar')->format('d/m/Y'));
                    $sheet->setCellValue('C' . $row, $item->user->name ?? 'Unknown');
                    $sheet->setCellValue('D' . $row, $item->created_at->timezone('Asia/Makassar')->format('H:i') . ' WITA');
                    $sheet->setCellValue('E' . $row, $jenisString);
                    $sheet->setCellValue('F' . $row, number_format($item->total_berat ?? 0, 2));
                    $sheet->setCellValue('G' . $row, number_format($item->jumlah, 0, ',', '.'));

                    // Simplified styling for large datasets
                    if ($processedCount % 100 === 0) {
                        // Apply basic styling every 100 rows to save memory
                        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => 'FF000000'],
                                ],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }

                    $totalBerat += $item->total_berat ?? 0;
                    $totalNilai += $item->jumlah;
                    $row++;

                    // Memory management: Clear processed items periodically
                    if ($processedCount % 200 === 0) {
                        unset($item);
                        gc_collect_cycles();
                    }
                }

                // Set summary (only for smaller datasets)
                if ($setoran->count() <= 10000) {
                    $summaryRow = $row + 1;
                    $sheet->mergeCells('A' . $summaryRow . ':E' . $summaryRow);
                    $sheet->setCellValue('A' . $summaryRow, 'TOTAL KESELURUHAN:');
                    $sheet->setCellValue('F' . $summaryRow, number_format($totalBerat, 2) . ' Kg');
                    $sheet->setCellValue('G' . $summaryRow, 'Rp ' . number_format($totalNilai, 0, ',', '.'));

                    // Styling summary
                    $sheet->getStyle('A' . $summaryRow . ':G' . $summaryRow)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF4CAF50'],
                            'endColor' => ['argb' => 'FF4CAF50'],
                        ],
                        'font' => [
                            'color' => ['argb' => 'FFFFFFFF'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Set column widths (simplified for large datasets)
                if ($setoran->count() <= 5000) {
                    $columnWidths = [8, 15, 25, 15, 30, 15, 20];
                    foreach ($columnWidths as $col => $width) {
                        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
                        $sheet->getColumnDimension($column)->setWidth($width);
                    }
                }

                // Skip freeze pane for very large datasets to save memory
                if ($setoran->count() <= 10000) {
                    $sheet->freezePane('A9');
                }

                // Save file
                $writer = new Xlsx($spreadsheet);
                $writer->save($filePath);

                // Cleanup
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                unset($sheet);

                return [
                    'total_records' => $setoran->count(),
                    'total_berat' => $totalBerat,
                    'total_nilai' => $totalNilai
                ];
            });

            Log::info("[{$requestId}] Large dataset Excel export completed", [
                'filename' => $filename,
                'file_size_mb' => round(filesize($filePath) / 1024 / 1024, 2),
                'processing_stats' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan Excel berhasil digenerate (Large Dataset)',
                'filename' => $filename,
                'download_url' => '/admin/export/download/' . urlencode($filename),
                'processing_type' => 'chunked',
                'summary' => [
                    'total_records' => $result['total_records'],
                    'total_berat' => number_format($result['total_berat'], 2),
                    'total_nilai' => number_format($result['total_nilai'], 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting Excel (Large Dataset): ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate laporan Excel: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Export laporan setoran ke Excel
     */
    public function exportExcel(Request $request): JsonResponse
    {
        $requestId = uniqid('export_');
        
        try {
            Log::info("[{$requestId}] Export Excel request received", [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data' => $request->all()
            ]);

            // Check dependencies and system requirements first
            $this->validateDependencies('excel');

            // Enhanced validasi input
            $validated = $request->validate([
                'tanggal_mulai' => [
                    'required',
                    'date',
                    'before_or_equal:tanggal_akhir',
                    'date_format:Y-m-d',
                    'before_or_equal:' . date('Y-m-d')
                ],
                'tanggal_akhir' => [
                    'required',
                    'date',
                    'after_or_equal:tanggal_mulai',
                    'date_format:Y-m-d',
                    'before_or_equal:' . date('Y-m-d')
                ]
            ], [
                'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
                'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh setelah tanggal akhir',
                'tanggal_mulai.date_format' => 'Format tanggal harus YYYY-MM-DD',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari ini',
                'tanggal_akhir.required' => 'Tanggal akhir wajib diisi',
                'tanggal_akhir.date' => 'Format tanggal akhir tidak valid',
                'tanggal_akhir.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai',
                'tanggal_akhir.date_format' => 'Format tanggal harus YYYY-MM-DD',
                'tanggal_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari hari ini'
            ]);

            $tanggalMulai = $request->tanggal_mulai;
            $tanggalAkhir = $request->tanggal_akhir;

            // Ambil data setoran
            $setoran = Setoran::with('user:id,name')
                ->whereBetween('created_at', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalAkhir . ' 23:59:59'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($setoran->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data setoran pada rentang tanggal yang dipilih'
                ], 404);
            }

            // Generate nama file
            $filename = $this->generateFilename('excel', $tanggalMulai, $tanggalAkhir);
            $filePath = storage_path('app/exports/' . $filename);

            // Pastikan direktori ada
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set header
            $this->setExcelHeader($sheet, $tanggalMulai, $tanggalAkhir);

            // Set data
            $row = 8; // Mulai dari baris 8 setelah header
            $totalBerat = 0;
            $totalNilai = 0;

            foreach ($setoran as $index => $item) {
                // Decode items jika ada
                $items = [];
                $jenisString = 'Sampah';
                if ($item->items) {
                    $items = json_decode($item->items, true);
                    if ($items && is_array($items)) {
                        $jenisItems = array_column($items, 'jenis');
                        $jenisString = implode(', ', $jenisItems);
                    }
                }

                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->created_at->timezone('Asia/Makassar')->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $item->user->name ?? 'Unknown');
                $sheet->setCellValue('D' . $row, $item->created_at->timezone('Asia/Makassar')->format('H:i') . ' WITA');
                $sheet->setCellValue('E' . $row, $jenisString);
                $sheet->setCellValue('F' . $row, number_format($item->total_berat ?? 0, 2));
                $sheet->setCellValue('G' . $row, number_format($item->jumlah, 0, ',', '.'));

                // Styling
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Highlight alternating rows
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF2F2F2'],
                        ],
                    ]);
                }

                $totalBerat += $item->total_berat ?? 0;
                $totalNilai += $item->jumlah;
                $row++;
            }

            // Set summary
            $summaryRow = $row + 1;
            $sheet->mergeCells('A' . $summaryRow . ':E' . $summaryRow);
            $sheet->setCellValue('A' . $summaryRow, 'TOTAL KESELURUHAN:');
            $sheet->setCellValue('F' . $summaryRow, number_format($totalBerat, 2) . ' Kg');
            $sheet->setCellValue('G' . $summaryRow, 'Rp ' . number_format($totalNilai, 0, ',', '.'));

            // Styling summary
            $sheet->getStyle('A' . $summaryRow . ':G' . $summaryRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4CAF50'],
                    'endColor' => ['argb' => 'FF4CAF50'],
                ],
                'font' => [
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Set column widths
            $columnWidths = [8, 15, 25, 15, 30, 15, 20];
            foreach ($columnWidths as $col => $width) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
                $sheet->getColumnDimension($column)->setWidth($width);
            }

            // Freeze pane
            $sheet->freezePane('A9');

            // Simpan file
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            // Cleanup
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            return response()->json([
                'success' => true,
                'message' => 'Laporan Excel berhasil digenerate',
                'filename' => $filename,
                'download_url' => '/admin/export/download/' . urlencode($filename),
                'summary' => [
                    'total_records' => $setoran->count(),
                    'total_berat' => number_format($totalBerat, 2),
                    'total_nilai' => number_format($totalNilai, 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error exporting Excel: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate laporan Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export laporan setoran ke PDF
     */
    public function exportPDF(Request $request): JsonResponse
    {
        $requestId = uniqid('export_pdf_');
        
        try {
            Log::info("[{$requestId}] Export PDF request received", [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data' => $request->all()
            ]);

            // Enhanced validasi input (sama dengan Excel)
            $validated = $request->validate([
                'tanggal_mulai' => [
                    'required',
                    'date',
                    'before_or_equal:tanggal_akhir',
                    'date_format:Y-m-d',
                    'before_or_equal:' . date('Y-m-d')
                ],
                'tanggal_akhir' => [
                    'required',
                    'date',
                    'after_or_equal:tanggal_mulai',
                    'date_format:Y-m-d',
                    'before_or_equal:' . date('Y-m-d')
                ]
            ], [
                'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
                'tanggal_mulai.date' => 'Format tanggal mulai tidak valid',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh setelah tanggal akhir',
                'tanggal_mulai.date_format' => 'Format tanggal harus YYYY-MM-DD',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari ini',
                'tanggal_akhir.required' => 'Tanggal akhir wajib diisi',
                'tanggal_akhir.date' => 'Format tanggal akhir tidak valid',
                'tanggal_akhir.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai',
                'tanggal_akhir.date_format' => 'Format tanggal harus YYYY-MM-DD',
                'tanggal_akhir.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari hari ini'
            ]);

            $tanggalMulai = $request->tanggal_mulai;
            $tanggalAkhir = $request->tanggal_akhir;

            // Ambil data setoran
            $setoran = Setoran::with('user:id,name')
                ->whereBetween('created_at', [
                    $tanggalMulai . ' 00:00:00',
                    $tanggalAkhir . ' 23:59:59'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($setoran->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data setoran pada rentang tanggal yang dipilih'
                ], 404);
            }

            // Generate nama file
            $filename = $this->generateFilename('pdf', $tanggalMulai, $tanggalAkhir);
            $filePath = storage_path('app/exports/' . $filename);

            // Pastikan direktori ada
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            // Setup PDF
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);

            // Buat HTML untuk PDF
            $html = $this->generatePDFHTML($setoran, $tanggalMulai, $tanggalAkhir);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Simpan file
            file_put_contents($filePath, $dompdf->output());

            // Cleanup
            unset($dompdf);

            return response()->json([
                'success' => true,
                'message' => 'Laporan PDF berhasil digenerate',
                'filename' => $filename,
                'download_url' => '/admin/export/download/' . urlencode($filename),
                'summary' => [
                    'total_records' => $setoran->count(),
                    'total_berat' => number_format($setoran->sum('total_berat'), 2),
                    'total_nilai' => number_format($setoran->sum('jumlah'), 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error exporting PDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate laporan PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file yang sudah digenerate
     */
    public function downloadFile($filename)
    {
        $requestId = uniqid('download_');
        
        try {
            Log::info("[{$requestId}] Download file request received", [
                'filename' => $filename,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            // Validasi filename
            if (empty($filename) || !is_string($filename)) {
                Log::warning("[{$requestId}] Invalid filename provided", ['filename' => $filename]);
                abort(400, 'Invalid filename');
            }

            // Sanitasi filename
            $filename = urldecode($filename);
            $filename = basename($filename); // Prevent directory traversal
            
            // Validasi format filename (harus sesuai pattern yang digenerate)
            if (!preg_match('/^Laporan_Setoran_\d{4}-\d{2}-\d{2}_\d{4}-\d{2}-\d{2}_\d{14}\.(xlsx|pdf)$/', $filename)) {
                Log::warning("[{$requestId}] Invalid filename format", ['filename' => $filename]);
                abort(400, 'Invalid filename format');
            }

            $filePath = storage_path('app/exports/' . $filename);

            // Cek apakah file exists
            if (!file_exists($filePath)) {
                Log::warning("[{$requestId}] File not found", ['filename' => $filename, 'path' => $filePath]);
                abort(404, 'File not found');
            }

            // Cek ukuran file (max 50MB)
            $fileSize = filesize($filePath);
            if ($fileSize > 50 * 1024 * 1024) { // 50MB
                Log::warning("[{$requestId}] File too large", ['filename' => $filename, 'size' => $fileSize]);
                abort(413, 'File too large');
            }

            // Determine file type dan validasi ekstensi
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $mimeTypes = [
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'pdf' => 'application/pdf'
            ];

            if (!isset($mimeTypes[$extension])) {
                Log::warning("[{$requestId}] Unsupported file type", ['filename' => $filename, 'extension' => $extension]);
                abort(415, 'Unsupported file type');
            }

            $mimeType = $mimeTypes[$extension];

            Log::info("[{$requestId}] File download successful", [
                'filename' => $filename,
                'file_size' => $fileSize,
                'mime_type' => $mimeType
            ]);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => $fileSize
            ]);

        } catch (\Exception $e) {
            Log::error("[{$requestId}] Error downloading file", [
                'filename' => $filename ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Internal server error during file download');
        }
    }

    /**
     * Set header Excel
     */
    private function setExcelHeader($sheet, $tanggalMulai, $tanggalAkhir)
    {
        // Header utama
        $sheet->setCellValue('A1', 'LAPORAN SETORAN SAMPAH');
        $sheet->setCellValue('A2', 'BANK SAMPAH UNIT MEKAR SWADAYA');
        $sheet->setCellValue('A3', 'Periode: ' . Carbon::parse($tanggalMulai)->format('d/m/Y') . ' - ' . Carbon::parse($tanggalAkhir)->format('d/m/Y'));
        $sheet->setCellValue('A4', 'Dicetak pada: ' . now()->timezone('Asia/Makassar')->format('d/m/Y H:i:s') . ' WITA');

        // Merging cells untuk header
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');

        // Styling header
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A3:A4')->applyFromArray([
            'font' => [
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Headers kolom
        $headers = ['No', 'Tanggal', 'Nama Nasabah', 'Waktu', 'Jenis Sampah', 'Total Berat (Kg)', 'Total Nilai (Rp)'];
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '7', $header);
        }

        // Styling headers
        $sheet->getStyle('A7:G7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2E7D32'],
                'endColor' => ['argb' => 'FF2E7D32'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Row spacing
        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(18);
        $sheet->getRowDimension('4')->setRowHeight(18);
        $sheet->getRowDimension('7')->setRowHeight(20);
    }

    /**
     * Generate HTML untuk PDF
     */
    private function generatePDFHTML($setoran, $tanggalMulai, $tanggalAkhir)
    {
        $totalBerat = $setoran->sum('total_berat');
        $totalNilai = $setoran->sum('jumlah');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Setoran Sampah</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    font-size: 11px;
                    line-height: 1.4;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #2E7D32; 
                    padding-bottom: 15px;
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 18px; 
                    color: #2E7D32; 
                    font-weight: bold;
                }
                .header h2 { 
                    margin: 5px 0; 
                    font-size: 14px; 
                    color: #4CAF50;
                }
                .header p { 
                    margin: 5px 0; 
                    font-size: 10px; 
                    color: #666;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin: 20px 0;
                    font-size: 9px;
                }
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 6px; 
                    text-align: center; 
                }
                th { 
                    background-color: #2E7D32; 
                    color: white; 
                    font-weight: bold;
                }
                tr:nth-child(even) { 
                    background-color: #f9f9f9; 
                }
                .summary { 
                    margin-top: 20px; 
                    padding: 15px; 
                    background-color: #E8F5E8; 
                    border: 1px solid #4CAF50; 
                    border-radius: 5px;
                }
                .summary h3 { 
                    margin: 0 0 10px 0; 
                    color: #2E7D32; 
                }
                .footer { 
                    margin-top: 30px; 
                    text-align: center; 
                    font-size: 9px; 
                    color: #666; 
                    border-top: 1px solid #ddd; 
                    padding-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN SETORAN SAMPAH</h1>
                <h2>BANK SAMPAH UNIT MEKAR SWADAYA</h2>
                <p>Periode: ' . Carbon::parse($tanggalMulai)->format('d/m/Y') . ' - ' . Carbon::parse($tanggalAkhir)->format('d/m/Y') . '</p>
                <p>Dicetak pada: ' . now()->timezone('Asia/Makassar')->format('d/m/Y H:i:s') . ' WITA</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Nasabah</th>
                        <th>Waktu</th>
                        <th>Jenis Sampah</th>
                        <th>Total Berat (Kg)</th>
                        <th>Total Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($setoran as $index => $item) {
            // Decode items jika ada
            $items = [];
            $jenisString = 'Sampah';
            if ($item->items) {
                $items = json_decode($item->items, true);
                if ($items && is_array($items)) {
                    $jenisItems = array_column($items, 'jenis');
                    $jenisString = implode(', ', $jenisItems);
                }
            }

            $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $item->created_at->timezone('Asia/Makassar')->format('d/m/Y') . '</td>
                        <td>' . ($item->user->name ?? 'Unknown') . '</td>
                        <td>' . $item->created_at->timezone('Asia/Makassar')->format('H:i') . ' WITA</td>
                        <td>' . $jenisString . '</td>
                        <td>' . number_format($item->total_berat ?? 0, 2) . '</td>
                        <td>Rp ' . number_format($item->jumlah, 0, ',', '.') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="summary">
                <h3>RINGKASAN LAPORAN</h3>
                <p><strong>Total Transaksi:</strong> ' . $setoran->count() . ' transaksi</p>
                <p><strong>Total Berat:</strong> ' . number_format($totalBerat, 2) . ' Kg</p>
                <p><strong>Total Nilai:</strong> Rp ' . number_format($totalNilai, 0, ',', '.') . '</p>
            </div>

            <div class="footer">
                <p>Laporan ini digenerate secara otomatis oleh sistem Bank Sampah Unit Mekar Swadaya</p>
                <p>Terima kasih atas kepercayaan Anda kepada Bank Sampah Unit Mekar Swadaya</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate nama file dengan timestamp
     */
    private function generateFilename($type, $tanggalMulai, $tanggalAkhir)
    {
        $startDate = Carbon::parse($tanggalMulai)->format('Y-m-d');
        $endDate = Carbon::parse($tanggalAkhir)->format('Y-m-d');
        $timestamp = now()->format('YmdHis');

        return "Laporan_Setoran_{$startDate}_{$endDate}_{$timestamp}.{$type}";
    }

    /**
     * Cleanup file export yang sudah lama (lebih dari 7 hari)
     * Bisa dipanggil via cron job atau manual
     */
    public function cleanupOldFiles(): JsonResponse
    {
        $requestId = uniqid('cleanup_');
        
        try {
            Log::info("[{$requestId}] Cleanup old export files started");

            $exportDir = storage_path('app/exports');
            
            if (!is_dir($exportDir)) {
                Log::info("[{$requestId}] Export directory does not exist", ['path' => $exportDir]);
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada direktori export untuk dibersihkan',
                    'cleaned_files' => 0,
                    'freed_space' => 0
                ]);
            }

            $files = glob($exportDir . '/*');
            $cutoffDate = now()->subDays(7);
            $cleanedCount = 0;
            $freedSpace = 0;

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileTime = filemtime($file);
                    $fileDate = Carbon::createFromTimestamp($fileTime);
                    
                    // Jika file lebih dari 7 hari, hapus
                    if ($fileDate->lt($cutoffDate)) {
                        $fileSize = filesize($file);
                        
                        if (unlink($file)) {
                            $cleanedCount++;
                            $freedSpace += $fileSize;
                            
                            Log::info("[{$requestId}] File deleted", [
                                'file' => basename($file),
                                'size' => $fileSize,
                                'age_days' => $cutoffDate->diffInDays($fileDate)
                            ]);
                        } else {
                            Log::warning("[{$requestId}] Failed to delete file", [
                                'file' => basename($file),
                                'error' => 'Permission denied or file locked'
                            ]);
                        }
                    }
                }
            }

            $freedSpaceMB = round($freedSpace / 1024 / 1024, 2);

            Log::info("[{$requestId}] Cleanup completed", [
                'cleaned_files' => $cleanedCount,
                'freed_space_mb' => $freedSpaceMB
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil membersihkan {$cleanedCount} file export lama",
                'cleaned_files' => $cleanedCount,
                'freed_space_mb' => $freedSpaceMB,
                'cutoff_date' => $cutoffDate->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error("[{$requestId}] Error during cleanup", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan file export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list file export yang tersedia
     * Untuk debugging atau monitoring
     */
    public function listExportFiles(): JsonResponse
    {
        $requestId = uniqid('list_');
        
        try {
            Log::info("[{$requestId}] List export files request received");

            $exportDir = storage_path('app/exports');
            
            if (!is_dir($exportDir)) {
                return response()->json([
                    'success' => true,
                    'files' => [],
                    'message' => 'Tidak ada direktori export'
                ]);
            }

            $files = glob($exportDir . '/*');
            $fileList = [];

            foreach ($files as $file) {
                if (is_file($file)) {
                    $stat = stat($file);
                    $fileInfo = [
                        'name' => basename($file),
                        'size' => $stat['size'],
                        'size_formatted' => $this->formatBytes($stat['size']),
                        'created' => date('Y-m-d H:i:s', $stat['ctime']),
                        'modified' => date('Y-m-d H:i:s', $stat['mtime']),
                        'age_hours' => round((time() - $stat['ctime']) / 3600, 1)
                    ];

                    // Extract info dari filename
                    if (preg_match('/^Laporan_Setoran_(\d{4}-\d{2}-\d{2})_(\d{4}-\d{2}-\d{2})_(\d{14})\.(xlsx|pdf)$/', $fileInfo['name'], $matches)) {
                        $fileInfo['period_start'] = $matches[1];
                        $fileInfo['period_end'] = $matches[2];
                        $fileInfo['file_type'] = $matches[4];
                    }

                    $fileList[] = $fileInfo;
                }
            }

            // Sort berdasarkan waktu creation (terbaru di atas)
            usort($fileList, function($a, $b) {
                return strcmp($b['created'], $a['created']);
            });

            $totalSize = array_sum(array_column($fileList, 'size'));

            Log::info("[{$requestId}] Export files listed", [
                'total_files' => count($fileList),
                'total_size_mb' => round($totalSize / 1024 / 1024, 2)
            ]);

            return response()->json([
                'success' => true,
                'files' => $fileList,
                'summary' => [
                    'total_files' => count($fileList),
                    'total_size' => $totalSize,
                    'total_size_formatted' => $this->formatBytes($totalSize)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("[{$requestId}] Error listing export files", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan daftar file export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes ke format yang readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
