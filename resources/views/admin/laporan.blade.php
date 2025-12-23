<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laporan - Bank Sampah Unit</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: #cfeede;
            color: #1f2c3a;
        }
        .layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            padding: 18px 16px;
            gap: 12px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .brand img { width: 42px; height: 42px; }
        .brand-text {
            font-weight: 800;
            font-size: 13px;
            line-height: 1.2;
            color: #0f6b2f;
        }
        .brand-sub { font-size: 11px; color: #7b7b7b; }
        .menu {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 6px;
        }
        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 6px;
            color: #8a8a8a;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            transition: color .15s ease;
            background: transparent;
        }
        .menu a.active {
            background: transparent;
            color: #0f6b2f;
        }
        .menu a:hover { color: #0f6b2f; }
        .menu .icon {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #1f1f1f;
        }
        .content {
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .page-title {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .page-title h1 { margin: 0; font-size: 28px; font-weight: 800; }
        .page-title p { margin: 0; color: #4b5563; font-weight: 500; }

        /* LAPORAN SECTION */
        .laporan-filter-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 14px 16px 16px;
            margin-bottom: 16px;
        }
        .laporan-filter-title {
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .laporan-filter-grid {
            display: grid;
            grid-template-columns: 1.4fr 1.1fr;
            gap: 10px 18px;
            align-items: flex-end;
        }
        .laporan-filter-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .laporan-label {
            font-size: 11px;
            color: #374151;
        }
        .laporan-select,
        .laporan-input-date {
            width: 100%;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            padding: 7px 9px;
            font-size: 12px;
            outline: none;
        }
        .laporan-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .btn-laporan-generate {
            padding: 7px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #14532d;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-laporan-generate:hover {
            background: #0f3b1f;
        }
        
        /* Export buttons styling */
        .btn-export-excel {
            padding: 7px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #16a34a;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-export-excel:hover {
            background: #15803d;
            transform: translateY(-1px);
        }
        
        .btn-export-excel:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-export-pdf {
            padding: 7px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #dc2626;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-export-pdf:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }
        
        .btn-export-pdf:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .laporan-report-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 14px 16px 18px;
        }
        .laporan-report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .laporan-report-title {
            font-size: 13px;
            font-weight: 800;
        }
        .btn-laporan-print {
            padding: 7px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #14532d;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-laporan-print:hover {
            background: #0f3b1f;
        }
        .laporan-table-wrapper {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            font-size: 11px;
            background: #f9fafb;
        }
        .laporan-table-header-row,
        .laporan-table-row {
            display: grid;
            grid-template-columns: 0.6fr 1.5fr 3fr 2.5fr 2fr 2.2fr 2.2fr;
            gap: 4px;
            padding: 4px 0;
        }
        .laporan-table-header-row {
            border-bottom: 1px solid #d1d5db;
            font-weight: 700;
        }
        .laporan-table-row:nth-child(odd) {
            background: #eef2f7;
        }
        .laporan-table-cell {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .laporan-table-footer {
            margin-top: 8px;
            font-size: 11px;
        }
        .laporan-total-row {
            display: flex;
            justify-content: flex-end;
            gap: 24px;
            margin-top: 6px;
            font-weight: 700;
        }
        .laporan-empty-text {
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            margin-top: 12px;
        }

        .back-link {
            margin-top: auto;
            padding: 12px 10px;
            font-size: 12px;
            color: #4b5563;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            align-self: flex-start;
        }
        .back-link:hover { color: #0f6b2f; text-decoration: underline; }

        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { flex-direction: row; flex-wrap: wrap; }
            .laporan-filter-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <img src="https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Logo.jpg" alt="Logo">
                <div>
                    <div class="brand-text">BANK SAMPAH UNIT MEKAR SWADAYA</div>
                    <div class="brand-sub">Pengelola</div>
                </div>
            </div>
            <nav class="menu">
                <a href="/admin/dashboard" class="menu-link"><span class="icon dashboard">▦</span>Dashboard</a>
                <a href="/admin/kelola-nasabah" class="menu-link"><span class="icon users">○</span>Kelola Nasabah</a>
                <a href="/admin/input-setoran" class="menu-link"><span class="icon plus">✚</span>Input Setoran</a>
                <a href="/admin/laporan" class="menu-link active"><span class="icon chart">▥</span>Laporan</a>
            </nav>
            
            <!-- Logout Button -->
            <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn" style="background: none; border: none; color: #dc2626; font-weight: 700; font-size: 12px; cursor: pointer; padding: 4px 2px; width: 100%; text-align: left; display: flex; align-items: center; gap: 12px;">Keluar</button>
                </form>
            </div>
            <a class="back-link" href="/">< Kembali ke Beranda</a>
        </aside>
        <main class="content">
            <!-- LAPORAN SECTION -->
            <div class="page-title" style="margin-bottom: 10px;">
                <h1>Laporan</h1>
                <p>Generate dan cetak laporan Bank Sampah</p>
            </div>

            <div class="laporan-filter-card">
                <div class="laporan-filter-title">Pengaturan Laporan</div>
                <div class="laporan-filter-grid">
                    <div class="laporan-filter-field">
                        <label class="laporan-label" for="laporan-jenis">Jenis Laporan</label>
                        <select id="laporan-jenis" class="laporan-select">
                            <option value="">Pilih jenis laporan</option>
                            <option value="setoran">Laporan Setoran Nasabah</option>
                        </select>
                    </div>
                    <div class="laporan-filter-field">
                        <label class="laporan-label" for="laporan-tgl-mulai">Tanggal Mulai</label>
                        <input id="laporan-tgl-mulai" type="date" class="laporan-input-date">
                    </div>
                    <div class="laporan-filter-field">
                        <label class="laporan-label" for="laporan-tgl-akhir">Tanggal Akhir</label>
                        <input id="laporan-tgl-akhir" type="date" class="laporan-input-date">
                    </div>
                    <div class="laporan-actions">
                        <button type="button" class="btn-laporan-generate" id="btn-laporan-generate">
                            Generate Laporan
                        </button>
                    </div>
                </div>
            </div>

            <div id="laporan-report-section" class="laporan-report-card hidden">
                <div class="laporan-report-header">
                    <div class="laporan-report-title">Laporan Setoran Sampah</div>
                    <div class="laporan-actions">
                        <button type="button" class="btn-export-excel" id="btn-export-excel" style="margin-right: 8px;">
                            📊 Export Excel
                        </button>
                        <button type="button" class="btn-export-pdf" id="btn-export-pdf" style="margin-right: 8px;">
                            📄 Export PDF
                        </button>
                        <button type="button" class="btn-laporan-print" id="btn-laporan-print">
                            🖨️ Print Laporan
                        </button>
                    </div>
                </div>

                <div class="laporan-table-wrapper">
                    <div class="laporan-table-header-row">
                        <div class="laporan-table-cell">No</div>
                        <div class="laporan-table-cell">Tanggal</div>
                        <div class="laporan-table-cell">Nama Nasabah</div>
                        <div class="laporan-table-cell">Waktu</div>
                        <div class="laporan-table-cell">Jenis</div>
                        <div class="laporan-table-cell">Total Berat (Kg)</div>
                        <div class="laporan-table-cell">Total Nilai (Rp)</div>
                    </div>
                    <div id="laporan-table-body"></div>
                    <div class="laporan-empty-text hidden" id="laporan-empty-text">
                        Tidak ada data pada rentang tanggal yang dipilih.
                    </div>
                    <div class="laporan-table-footer">
                        <div class="laporan-total-row">
                            <span id="laporan-total-berat-label">Total Berat: 0.00 Kg</span>
                            <span id="laporan-total-nilai-label">Total Nilai: Rp0</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // LAPORAN Functions
        (function () {
            var jenisSelect = document.getElementById('laporan-jenis');
            var tglMulaiInput = document.getElementById('laporan-tgl-mulai');
            var tglAkhirInput = document.getElementById('laporan-tgl-akhir');
            var btnGenerate = document.getElementById('btn-laporan-generate');
            var reportSection = document.getElementById('laporan-report-section');
            var tableBody = document.getElementById('laporan-table-body');
            var emptyText = document.getElementById('laporan-empty-text');
            var totalBeratLabel = document.getElementById('laporan-total-berat-label');
            var totalNilaiLabel = document.getElementById('laporan-total-nilai-label');
            var btnPrint = document.getElementById('btn-laporan-print');
            
            // Export buttons
            var btnExportExcel = document.getElementById('btn-export-excel');
            var btnExportPDF = document.getElementById('btn-export-pdf');
            
            // Store current report data for export
            var currentReportData = null;
            var currentDateRange = null;

            function formatRupiah(n) {
                return 'Rp' + (n || 0).toLocaleString('id-ID');
            }

            function showLoading(element) {
                if (element) {
                    element.innerHTML = '<div style="padding: 20px; text-align: center; color: #9ca3af;">🔄 Memuat data laporan...</div>';
                }
            }

            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 600;
                    font-size: 12px;
                    z-index: 9999;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    max-width: 300px;
                    word-wrap: break-word;
                    animation: slideInRight 0.3s ease;
                `;

                if (type === 'error') {
                    notification.style.backgroundColor = '#dc2626';
                } else if (type === 'success') {
                    notification.style.backgroundColor = '#16a34a';
                } else if (type === 'warning') {
                    notification.style.backgroundColor = '#d97706';
                } else {
                    notification.style.backgroundColor = '#2563eb';
                }

                notification.textContent = message;
                document.body.appendChild(notification);

                const style = document.createElement('style');
                style.textContent = `
                    @keyframes slideInRight {
                        from {
                            opacity: 0;
                            transform: translateX(100%);
                        }
                        to {
                            opacity: 1;
                            transform: translateX(0);
                        }
                    }
                `;
                document.head.appendChild(style);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    notification.style.transition = 'all 0.3s ease';
                }, 3000);

                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 3300);
            }

            function renderLaporan(data) {
                if (!tableBody || !emptyText || !reportSection) return;

                tableBody.innerHTML = '';

                if (!data.length) {
                    emptyText.classList.remove('hidden');
                } else {
                    emptyText.classList.add('hidden');
                }

                data.forEach(function (row, idx) {
                    // Data sudah dalam format yang benar dari API
                    var berat = parseFloat(row.total_berat) || 0;
                    
                    var el = document.createElement('div');
                    el.className = 'laporan-table-row';
                    el.innerHTML =
                        '<div class="laporan-table-cell">' + row.no + '</div>' +
                        '<div class="laporan-table-cell">' + row.tanggal + '</div>' +
                        '<div class="laporan-table-cell">' + row.nama_nasabah + '</div>' +
                        '<div class="laporan-table-cell">' + row.waktu + '</div>' +
                        '<div class="laporan-table-cell">' + row.jenis + '</div>' +
                        '<div class="laporan-table-cell">' + berat.toFixed(2) + '</div>' +
                        '<div class="laporan-table-cell">Rp ' + row.total_nilai + '</div>';
                    tableBody.appendChild(el);
                });

                reportSection.classList.remove('hidden');
            }

            // Enhanced export functions dengan endpoint yang benar dan error handling yang lebih baik
            function exportToExcel() {
                if (!currentReportData || currentReportData.length === 0) {
                    showNotification('Tidak ada data untuk diekspor. Silakan generate laporan terlebih dahulu.', 'warning');
                    return;
                }

                // Show loading state
                btnExportExcel.disabled = true;
                const originalText = btnExportExcel.textContent;
                btnExportExcel.textContent = '🔄 Menyiapkan Excel...';

                // Prepare data for export
                var exportData = {
                    tanggal_mulai: currentDateRange.start,
                    tanggal_akhir: currentDateRange.end
                };

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                console.log('📊 Starting Excel export...');
                console.log('📋 Export data:', exportData);
                console.log('🛡️ CSRF Token:', csrfToken ? 'Present' : 'Missing');

                // FIXED: Use correct API endpoint
                fetch('/api/admin/export/excel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(exportData)
                })
                .then(response => {
                    console.log('📡 Export API Response Status:', response.status);
                    console.log('📡 Response Headers:', [...response.headers.entries()]);
                    
                    // Check if response is HTML (error page) instead of JSON
                    const contentType = response.headers.get('content-type');
                    console.log('📡 Content-Type:', contentType);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status} - ${response.statusText}`);
                    }
                    
                    if (contentType && contentType.includes('text/html')) {
                        // Response is HTML error page, not JSON
                        throw new Error('Server mengembalikan HTML error page. Kemungkinan routing atau authentication error.');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Export API Response Data:', data);
                    
                    if (data.success) {
                        // Create download link dengan error handling
                        const downloadUrl = data.download_url || '/api/admin/export/download/' + encodeURIComponent(data.filename);
                        console.log('🔗 Download URL:', downloadUrl);
                        
                        // Direct download without testing URL accessibility
                        window.open(downloadUrl, '_blank');
                        showNotification('✅ File Excel berhasil digenerate dan didownload!', 'success');
                    } else {
                        throw new Error(data.message || 'Server mengembalikan error saat generate Excel');
                    }
                })
                .catch(error => {
                    console.error('❌ Excel Export Error:', error);
                    
                    let errorMessage = '❌ Gagal mengexport ke Excel: ';
                    
                    if (error.message.includes('HTML error page')) {
                        errorMessage += 'Masalah routing atau authentication. Pastikan login sebagai admin dan coba refresh halaman.';
                    } else if (error.message.includes('HTTP Error: 404')) {
                        errorMessage += 'Endpoint tidak ditemukan. Pastikan server Laravel berjalan dengan benar.';
                    } else if (error.message.includes('HTTP Error: 419')) {
                        errorMessage += 'CSRF token expired. Refresh halaman dan coba lagi.';
                    } else if (error.message.includes('HTTP Error: 500')) {
                        errorMessage += 'Internal server error. Periksa log Laravel untuk detail.';
                    } else if (error.message.includes('Network')) {
                        errorMessage += 'Masalah jaringan. Periksa koneksi internet Anda.';
                    } else {
                        errorMessage += error.message;
                    }
                    
                    showNotification(errorMessage, 'error');
                    
                    // Show detailed error in console for debugging
                    console.group('🔍 Debug Information');
                    console.log('Error Type:', error.constructor.name);
                    console.log('Error Message:', error.message);
                    console.log('Current URL:', window.location.href);
                    console.log('Export Data:', exportData);
                    console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
                    console.groupEnd();
                })
                .finally(() => {
                    // Reset button
                    btnExportExcel.disabled = false;
                    btnExportExcel.textContent = originalText;
                });
            }

            function exportToPDF() {
                if (!currentReportData || currentReportData.length === 0) {
                    showNotification('Tidak ada data untuk diekspor. Silakan generate laporan terlebih dahulu.', 'warning');
                    return;
                }

                // Show loading
                btnExportPDF.disabled = true;
                btnExportPDF.textContent = '🔄 Menyiapkan PDF...';

                // Prepare data for export
                var exportData = {
                    tanggal_mulai: currentDateRange.start,
                    tanggal_akhir: currentDateRange.end
                };

                // Call export API - FIXED: Use correct endpoint
                fetch('/api/export/pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(exportData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Download the file
                        var downloadUrl = data.download_url || '/admin/export/download/' + encodeURIComponent(data.filename);
                        window.open(downloadUrl, '_blank');
                        
                        showNotification('✅ File PDF berhasil digenerate dan didownload!', 'success');
                    } else {
                        throw new Error(data.message || 'Gagal mengexport ke PDF');
                    }
                })
                .catch(error => {
                    console.error('Error exporting PDF:', error);
                    showNotification('❌ Gagal mengexport ke PDF: ' + error.message, 'error');
                })
                .finally(() => {
                    // Reset button
                    btnExportPDF.disabled = false;
                    btnExportPDF.textContent = '📄 Export PDF';
                });
            }

            // Event listeners for export buttons
            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', exportToExcel);
            }

            if (btnExportPDF) {
                btnExportPDF.addEventListener('click', exportToPDF);
            }

            if (btnGenerate) {
                btnGenerate.addEventListener('click', function () {
                    if (!jenisSelect || !jenisSelect.value) {
                        showNotification('Silakan pilih jenis laporan terlebih dahulu', 'error');
                        return;
                    }

                    if (!tglMulaiInput || !tglMulaiInput.value) {
                        showNotification('Silakan pilih tanggal mulai', 'error');
                        return;
                    }

                    if (!tglAkhirInput || !tglAkhirInput.value) {
                        showNotification('Silakan pilih tanggal akhir', 'error');
                        return;
                    }

                    // Validate date range
                    var startDate = new Date(tglMulaiInput.value);
                    var endDate = new Date(tglAkhirInput.value);
                    
                    if (startDate > endDate) {
                        showNotification('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 'error');
                        return;
                    }

                    // Show loading
                    if (tableBody) showLoading(tableBody);
                    reportSection.classList.remove('hidden');
                    
                    // Disable button
                    btnGenerate.disabled = true;
                    btnGenerate.textContent = 'Memproses...';

                    // Prepare API request data
                    var requestData = {
                        tanggal_mulai: tglMulaiInput.value,
                        tanggal_akhir: tglAkhirInput.value
                    };

                    // Store date range for export
                    currentDateRange = {
                        start: tglMulaiInput.value,
                        end: tglAkhirInput.value
                    };

                    // Call API untuk generate laporan
                    fetch('/api/admin/laporan', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(requestData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Store report data for export
                            currentReportData = data.data || [];
                            
                            // Render laporan dengan data dari API
                            renderLaporan(currentReportData);
                            
                            // Update summary dari API jika ada
                            if (data.summary) {
                                if (totalBeratLabel) totalBeratLabel.textContent = 'Total Berat: ' + data.summary.total_berat + ' Kg';
                                if (totalNilaiLabel) totalNilaiLabel.textContent = 'Total Nilai: Rp ' + data.summary.total_nilai;
                            }
                            
                            showNotification('✅ Laporan berhasil digenerate! Data siap untuk diekspor.', 'success');
                        } else {
                            throw new Error(data.message || 'Gagal generate laporan');
                        }
                    })
                    .catch(error => {
                        console.error('Error generating laporan:', error);
                        
                        // Show error state
                        if (tableBody) {
                            tableBody.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc2626;">❌ Gagal memuat data laporan: ' + error.message + '</div>';
                        }
                        emptyText.classList.remove('hidden');
                        
                        showNotification('❌ Gagal generate laporan. Silakan coba lagi.', 'error');
                    })
                    .finally(() => {
                        // Re-enable button
                        btnGenerate.disabled = false;
                        btnGenerate.textContent = 'Generate Laporan';
                    });
                });
            }

            if (btnPrint) {
                btnPrint.addEventListener('click', function () {
                    window.print();
                });
            }

            // Set default dates (current month)
            function setDefaultDates() {
                var now = new Date();
                var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                var lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                
                if (tglMulaiInput) tglMulaiInput.value = firstDay.toISOString().split('T')[0];
                if (tglAkhirInput) tglAkhirInput.value = lastDay.toISOString().split('T')[0];
            }

            // Initialize default dates
            setDefaultDates();
        })();
    </script>
</body>
</html>
