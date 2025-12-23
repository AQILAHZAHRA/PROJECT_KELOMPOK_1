<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Input Setoran - Bank Sampah Unit</title>
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

        /* INPUT SETORAN SECTION */
        .setoran-layout {
            display: grid;
            grid-template-columns: 2fr 1.3fr;
            gap: 18px;
            align-items: flex-start;
        }
        .setoran-left {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .setoran-step-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 14px 16px;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
        }
        .setoran-step-number {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #14532d;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }
        .setoran-step-body-title {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .setoran-step-body-sub {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .setoran-field-label {
            font-size: 11px;
            color: #374151;
            margin-bottom: 4px;
        }
        .setoran-select,
        .setoran-input {
            width: 100%;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            padding: 7px 9px;
            font-size: 12px;
            outline: none;
        }
        .setoran-select {
            background-color: #f9fafb;
        }
        .setoran-small-note {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .setoran-form-row {
            display: grid;
            grid-template-columns: 2fr 1.2fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .btn-setoran-add {
            margin-top: 4px;
            padding: 7px 14px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #14532d;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-setoran-add:hover {
            background: #0f3b1f;
        }
        .setoran-items-title {
            font-size: 12px;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        .setoran-items-list {
            border-radius: 6px;
            background: #14532d;
            color: #ffffff;
            padding: 8px 10px;
            font-size: 11px;
        }
        .setoran-items-list.empty {
            background: #f3f4f6;
            color: #9ca3af;
        }
        .setoran-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 2px;
        }
        .setoran-item-main {
            font-weight: 600;
        }
        .setoran-item-sub {
            font-size: 11px;
        }

        .setoran-right {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 14px 16px;
        }
        .setoran-summary-title {
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .setoran-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 12px;
        }
        .setoran-summary-label {
            color: #374151;
        }
        .setoran-summary-value {
            font-weight: 700;
        }
        .btn-setoran-submit {
            margin-top: 10px;
            width: 100%;
            padding: 8px 14px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #14532d;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
        }
        .btn-setoran-submit:hover {
            background: #0f3b1f;
        }
        .setoran-summary-note {
            margin-top: 4px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }

        /* Modal sukses transaksi */
        .setoran-success-title {
            font-size: 13px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 6px;
        }
        .setoran-success-text {
            font-size: 11px;
            text-align: center;
            color: #4b5563;
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
            .setoran-layout { grid-template-columns: 1fr; }
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
                <a href="/admin/input-setoran" class="menu-link active"><span class="icon plus">✚</span>Input Setoran</a>
                <a href="/admin/laporan" class="menu-link"><span class="icon chart">▥</span>Laporan</a>
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
            <!-- INPUT SETORAN SECTION -->
            <div class="page-title" style="margin-bottom: 10px;">
                <h1>Input Setoran Sampah</h1>
                <p>Catat transaksi setoran sampah dari nasabah</p>
            </div>

            <div class="setoran-layout">
                <div class="setoran-left">
                    <!-- Step 1: Identifikasi Nasabah -->
                    <div class="setoran-step-card">
                        <div class="setoran-step-number">1</div>
                        <div>
                            <div class="setoran-step-body-title">Identifikasi Nasabah</div>
                            <div class="setoran-step-body-sub">Pilih nasabah penyetor sampah</div>

                            <div class="setoran-field">
                                <div class="setoran-field-label">Pilih Nasabah</div>
                                <select id="setoran-nasabah" class="setoran-select">
                                    <option value="">-- Pilih Nasabah --</option>
                                </select>
                                <div class="setoran-small-note" id="setoran-saldo-awal">
                                    Saldo saat ini: Rp0
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Input Data Setoran -->
                    <div class="setoran-step-card">
                        <div class="setoran-step-number">2</div>
                        <div>
                            <div class="setoran-step-body-title">Input Data Setoran</div>
                            <div class="setoran-step-body-sub">Masukkan jenis dan berat sampah</div>

                            <div class="setoran-form-row">
                                <div>
                                    <div class="setoran-field-label">Jenis Sampah</div>
                                    <input id="setoran-jenis" type="text" class="setoran-input" placeholder="Masukkan jenis sampah (contoh: Plastik HDPE, Kertas Koran, Besi Tua)">
                                </div>
                                <div>
                                    <div class="setoran-field-label">Berat (Kg)</div>
                                    <input id="setoran-berat" type="number" min="0" step="0.01" class="setoran-input" placeholder="Contoh 1.5">
                                </div>
                            </div>
                            <div class="setoran-form-row">
                                <div>
                                    <div class="setoran-field-label">Harga per Kg (Rp)</div>
                                    <input id="setoran-harga" type="number" min="0" step="100" class="setoran-input" placeholder="Contoh 3000">
                                </div>
                                <div style="display: flex; align-items: end;">
                                    <button type="button" class="btn-setoran-add" id="btn-setoran-tambah-item">+ Tambah Item</button>
                                </div>
                            </div>
                            <div class="setoran-items-title">Daftar item setoran:</div>
                            <div class="setoran-items-list empty" id="setoran-items-list">
                                Belum ada item setoran yang ditambahkan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Ringkasan -->
                <div class="setoran-right">
                    <div class="setoran-step-number" style="margin-bottom: 8px;">3</div>
                    <div class="setoran-summary-title">Ringkasan</div>

                    <div class="setoran-summary-row">
                        <span class="setoran-summary-label">Total Berat</span>
                        <span class="setoran-summary-value" id="setoran-total-berat">0.00 Kg</span>
                    </div>
                    <div class="setoran-summary-row">
                        <span class="setoran-summary-label">Total Nilai</span>
                        <span class="setoran-summary-value" id="setoran-total-nilai">Rp0</span>
                    </div>
                    <hr style="margin: 10px 0; border-top: 1px solid #e5e7eb;">
                    <div class="setoran-summary-row">
                        <span class="setoran-summary-label">Saldo setelah transaksi</span>
                        <span class="setoran-summary-value" id="setoran-saldo-setelah">Rp0</span>
                    </div>

                    <button type="button" class="btn-setoran-submit" id="btn-setoran-simpan">
                        Simpan Transaksi
                    </button>
                    <div class="setoran-summary-note">
                        Tambahkan minimal satu item untuk melanjutkan
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Select2 CSS dan JS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Input Setoran Functions
        (function () {
            var setoranItems = [];
            var totalBerat = 0;
            var totalNilai = 0;

            // Initialize Select2 for dropdown
            $(document).ready(function() {
                $('#setoran-nasabah').select2({
                    placeholder: "-- Pilih Nasabah --",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Tidak ada data nasabah";
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
            });

            // Fungsi untuk mengambil data nasabah dari API
            function loadNasabahData() {
                fetch('/api/admin/search-nasabah', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.success) {
                            const dropdown = document.getElementById('setoran-nasabah');
                            if (dropdown) {
                                // Clear existing options (except the first one)
                                dropdown.innerHTML = '<option value="">-- Pilih Nasabah --</option>';

                                // Add all nasabah to dropdown
                                response.data.forEach(function(nasabah) {
                                    const option = document.createElement('option');
                                    option.value = nasabah.id;
                                    option.textContent = `${nasabah.name} - ${nasabah.no_hp || 'N/A'}`;
                                    option.dataset.saldo = parseInt(nasabah.saldo) || 0;
                                    dropdown.appendChild(option);
                                });

                                // Refresh Select2
                                $('#setoran-nasabah').select2('destroy');
                                $('#setoran-nasabah').select2({
                                    placeholder: "-- Pilih Nasabah --",
                                    allowClear: true,
                                    width: '100%',
                                    language: {
                                        noResults: function() {
                                            return "Tidak ada data nasabah";
                                        }
                                    }
                                });
                            }
                        } else {
                            throw new Error(response.message || 'Failed to load data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading nasabah data:', error);
                        showNotification('Gagal memuat data nasabah: ' + error.message, 'error');
                    });
            }

            function formatRupiah(n) {
                return 'Rp' + (n || 0).toLocaleString('id-ID');
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

            function updateSetoranSummary() {
                const totalBeratEl = document.getElementById('setoran-total-berat');
                const totalNilaiEl = document.getElementById('setoran-total-nilai');
                const saldoSetelahEl = document.getElementById('setoran-saldo-setelah');
                
                if (totalBeratEl) totalBeratEl.textContent = totalBerat.toFixed(2) + ' Kg';
                if (totalNilaiEl) totalNilaiEl.textContent = formatRupiah(totalNilai);
                
                const nasabahSelect = document.getElementById('setoran-nasabah');
                if (nasabahSelect && saldoSetelahEl) {
                    const selectedOption = $('#setoran-nasabah option:selected')[0];
                    if (selectedOption && selectedOption.dataset && selectedOption.dataset.saldo) {
                        const saldoAwal = parseInt(selectedOption.dataset.saldo) || 0;
                        const saldoSekarang = saldoAwal + totalNilai;
                        saldoSetelahEl.textContent = formatRupiah(saldoSekarang);
                    } else {
                        saldoSetelahEl.textContent = formatRupiah(totalNilai);
                    }
                }
            }

            function renderSetoranItems() {
                const itemsList = document.getElementById('setoran-items-list');
                if (!itemsList) return;

                if (setoranItems.length === 0) {
                    itemsList.className = 'setoran-items-list empty';
                    itemsList.textContent = 'Belum ada item setoran yang ditambahkan';
                    return;
                }

                itemsList.className = 'setoran-items-list';
                let html = '';
                setoranItems.forEach(function(item, index) {
                    html += `
                        <div class="setoran-item-row">
                            <div>
                                <div class="setoran-item-main">${item.jenis}</div>
                                <div class="setoran-item-sub">${item.berat} Kg x Rp ${item.harga.toLocaleString('id-ID')} = ${formatRupiah(item.nilai)}</div>
                            </div>
                            <button type="button" onclick="removeSetoranItem(${index})" style="background: #dc2626; color: white; border: none; padding: 2px 6px; border-radius: 3px; cursor: pointer; font-size: 10px;">×</button>
                        </div>
                    `;
                });
                itemsList.innerHTML = html;
                updateSetoranSummary();
            }

            function removeSetoranItem(index) {
                const item = setoranItems[index];
                totalBerat -= parseFloat(item.berat);
                totalNilai -= item.nilai;
                setoranItems.splice(index, 1);
                renderSetoranItems();
            }

            window.removeSetoranItem = removeSetoranItem;

            // Handle tombol tambah item
            const btnTambahItem = document.getElementById('btn-setoran-tambah-item');
            if (btnTambahItem) {
                btnTambahItem.addEventListener('click', function() {
                    const jenisEl = document.getElementById('setoran-jenis');
                    const beratEl = document.getElementById('setoran-berat');
                    const hargaEl = document.getElementById('setoran-harga');

                    if (!jenisEl || !beratEl || !hargaEl) return;

                    const jenis = jenisEl.value.trim();
                    const berat = parseFloat(beratEl.value);
                    const harga = parseInt(hargaEl.value);

                    // Validation
                    if (!jenis) {
                        showNotification('Jenis sampah wajib diisi', 'error');
                        return;
                    }
                    if (!berat || berat <= 0) {
                        showNotification('Berat harus lebih dari 0', 'error');
                        return;
                    }
                    if (!harga || harga <= 0) {
                        showNotification('Harga per Kg harus lebih dari 0', 'error');
                        return;
                    }

                    const nilai = berat * harga;

                    // Add item
                    setoranItems.push({
                        jenis: jenis,
                        berat: berat,
                        harga: harga,
                        nilai: nilai
                    });

                    // Update totals
                    totalBerat += berat;
                    totalNilai += nilai;

                    // Clear form
                    jenisEl.value = '';
                    beratEl.value = '';
                    hargaEl.value = '';

                    // Render items
                    renderSetoranItems();

                    showNotification('Item berhasil ditambahkan', 'success');
                });
            }

            // Handle setoran submit
            const btnSetoranSimpan = document.getElementById('btn-setoran-simpan');
            if (btnSetoranSimpan) {
                btnSetoranSimpan.addEventListener('click', function() {
                    const nasabahSelect = document.getElementById('setoran-nasabah');
                    
                    if (!nasabahSelect || !nasabahSelect.value) {
                        showNotification('Silakan pilih nasabah terlebih dahulu', 'error');
                        return;
                    }
                    if (setoranItems.length === 0) {
                        showNotification('Silakan tambahkan minimal satu item setoran', 'error');
                        return;
                    }

                    // Show confirmation
                    if (!confirm('Simpan transaksi setoran ini?')) {
                        return;
                    }

                    // Disable button
                    btnSetoranSimpan.disabled = true;
                    btnSetoranSimpan.textContent = 'Menyimpan...';

                    // Prepare data
                    const requestData = {
                        user_id: parseInt(nasabahSelect.value),
                        items: setoranItems,
                        total_berat: totalBerat,
                        total_nilai: totalNilai
                    };

                    // Submit to API
                    fetch('/api/admin/setoran', {
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
                            showNotification('Transaksi setoran berhasil disimpan', 'success');
                            
                            // Reset form
                            setoranItems = [];
                            totalBerat = 0;
                            totalNilai = 0;
                            
                            document.getElementById('setoran-nasabah').value = '';
                            document.getElementById('setoran-jenis').value = '';
                            document.getElementById('setoran-berat').value = '';
                            document.getElementById('setoran-harga').value = '';
                            
                            // Refresh dropdown
                            $('#setoran-nasabah').select2('destroy');
                            $('#setoran-nasabah').select2({
                                placeholder: "-- Pilih Nasabah --",
                                allowClear: true,
                                width: '100%'
                            });
                            
                            renderSetoranItems();
                        } else {
                            throw new Error(data.message || 'Gagal menyimpan transaksi');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving setoran:', error);
                        showNotification('Gagal menyimpan transaksi: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Re-enable button
                        btnSetoranSimpan.disabled = false;
                        btnSetoranSimpan.textContent = 'Simpan Transaksi';
                    });
                });
            }

            // Handle dropdown nasabah change
            const nasabahSelect = document.getElementById('setoran-nasabah');
            if (nasabahSelect) {
                $('#setoran-nasabah').on('change', function() {
                    const saldoAwalEl = document.getElementById('setoran-saldo-awal');
                    if (saldoAwalEl) {
                        const selectedOption = $(this).find('option:selected')[0];
                        if (selectedOption && selectedOption.dataset && selectedOption.dataset.saldo) {
                            const saldo = parseInt(selectedOption.dataset.saldo) || 0;
                            saldoAwalEl.textContent = 'Saldo saat ini: ' + formatRupiah(saldo);
                        } else {
                            saldoAwalEl.textContent = 'Saldo saat ini: Rp0';
                        }
                    }
                    updateSetoranSummary();
                });
            }

            // Load data saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                loadNasabahData();
            });
        })();
    </script>
</body>
</html>
