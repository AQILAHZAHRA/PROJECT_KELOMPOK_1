<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelola Nasabah - Bank Sampah Unit</title>
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

        /* KELOLA NASABAH SECTION */
        .kelola-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .kelola-header-title {
            font-size: 22px;
            font-weight: 800;
            color: #1f2c3a;
        }
        .btn-add-nasabah {
            padding: 7px 16px;
            background: #0f6b2f;
            color: #ffffff;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .btn-add-nasabah:hover {
            background: #0b5224;
        }
        .search-box {
            margin-bottom: 14px;
        }
        .search-input-wrapper {
            background: #fdfdfd;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-input-wrapper span {
            font-size: 14px;
            color: #9ca3af;
        }
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 12px;
            color: #374151;
        }
        .kelola-panel {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 0;
            overflow: hidden;
        }
        .kelola-table-header {
            background: #14532d;
            color: #ffffff;
            display: grid;
            grid-template-columns: 2fr 3fr 2fr 2fr 1.5fr;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 18px;
        }
        .kelola-table-body {
            min-height: 260px;
            background: #ffffff;
            position: relative;
        }
        .kelola-empty {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 12px;
        }
        .kelola-empty-icon {
            font-size: 26px;
            margin-bottom: 6px;
        }
        .kelola-table-rows {
            display: flex;
            flex-direction: column;
        }
        .kelola-row {
            display: grid;
            grid-template-columns: 2fr 3fr 2fr 2fr 1.5fr;
            font-size: 12px;
            padding: 8px 18px;
            border-top: 1px solid #e5e7eb;
            align-items: center;
        }
        .kelola-row:nth-child(even) {
            background: #f9fafb;
        }
        .kelola-cell {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .kelola-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .icon-action {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 14px;
        }
        .icon-edit {
            color: #065f46;
        }
        .icon-delete {
            color: #b91c1c;
        }

        /* MODAL TAMBAH NASABAH */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 40;
        }
        .modal-card {
            width: 420px;
            max-width: 95%;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            padding: 16px 18px 18px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .modal-title {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }
        .modal-close {
            border: none;
            background: transparent;
            font-size: 18px;
            cursor: pointer;
            color: #6b7280;
        }
        .modal-field {
            margin-bottom: 10px;
        }
        .modal-label {
            font-size: 12px;
            margin-bottom: 4px;
            color: #374151;
            display: block;
        }
        .modal-input,
        .modal-textarea {
            width: 100%;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            padding: 7px 9px;
            font-size: 12px;
            outline: none;
        }
        .modal-textarea {
            height: 80px;
            resize: none;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 16px;
        }
        .btn-modal {
            min-width: 110px;
            padding: 7px 14px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            color: #ffffff;
        }
        .btn-modal-cancel {
            background: #dc2626;
        }
        .btn-modal-cancel:hover {
            background: #b91c1c;
        }
        .btn-modal-save {
            background: #14532d;
        }
        .btn-modal-save:hover {
            background: #0f3b1f;
        }
        .btn-modal-secondary {
            background: #2563eb;
        }
        .btn-modal-secondary:hover {
            background: #1d4ed8;
        }

        .modal-card.modal-small {
            width: 360px;
        }
        .delete-message {
            text-align: center;
            font-size: 13px;
            color: #111827;
            margin-top: 8px;
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
            .kelola-table-header, .kelola-row {
                grid-template-columns: 1fr;
            }
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
                <a href="/admin/kelola-nasabah" class="menu-link active"><span class="icon users">○</span>Kelola Nasabah</a>
                <a href="/admin/input-setoran" class="menu-link"><span class="icon plus">✚</span>Input Setoran</a>
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
            <!-- KELOLA NASABAH SECTION -->
            <div class="kelola-header">
                <div class="kelola-header-title">Kelola Nasabah</div>
                <button type="button" class="btn-add-nasabah" id="btn-open-tambah-nasabah">
                    + Tambah Nasabah
                </button>
            </div>

            <div class="search-box">
                <div class="search-input-wrapper">
                    <span>🔍</span>
                    <input
                        type="text"
                        class="search-input"
                        placeholder="Cari nasabah berdasarkan nama, no. HP, atau alamat..."
                    >
                </div>
            </div>

            <div class="kelola-panel">
                <div class="kelola-table-header">
                    <div>Nama</div>
                    <div>Alamat</div>
                    <div>No. HP</div>
                    <div>Saldo</div>
                    <div>Aksi</div>
                </div>
                <div class="kelola-table-body" id="kelola-table-body">
                    <div class="kelola-empty" id="kelola-table-empty">
                        <div class="kelola-empty-icon">👤</div>
                        Belum ada data setoran
                    </div>
                    <div class="kelola-table-rows" id="kelola-table-rows"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH NASABAH -->
    <div id="modal-tambah-nasabah" class="modal-backdrop hidden">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Tambah Nasabah Baru</div>
                <button type="button" class="modal-close" id="modal-close-tambah">&times;</button>
            </div>

            <form id="form-tambah-nasabah">
                <div class="modal-field">
                    <label class="modal-label" for="nama_nasabah">Nama Lengkap</label>
                    <input id="nama_nasabah" type="text" class="modal-input" placeholder="Masukkan nama lengkap">
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="alamat_nasabah">Alamat</label>
                    <textarea id="alamat_nasabah" class="modal-textarea" placeholder="Masukkan alamat lengkap"></textarea>
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="hp_nasabah">No. HP</label>
                    <input id="hp_nasabah" type="text" class="modal-input" placeholder="Contoh: 08xxxxxxxxxx">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-modal btn-modal-cancel" id="modal-btn-cancel">
                        Batal
                    </button>
                    <button type="submit" class="btn-modal btn-modal-save" id="modal-btn-save">
                        Tambah Nasabah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT NASABAH -->
    <div id="modal-edit-nasabah" class="modal-backdrop hidden">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit Data Nasabah</div>
                <button type="button" class="modal-close" id="modal-close-edit">&times;</button>
            </div>

            <form id="form-edit-nasabah">
                <div class="modal-field">
                    <label class="modal-label" for="edit_nama_nasabah">Nama Lengkap</label>
                    <input id="edit_nama_nasabah" type="text" class="modal-input">
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="edit_alamat_nasabah">Alamat</label>
                    <textarea id="edit_alamat_nasabah" class="modal-textarea"></textarea>
                </div>

                <div class="modal-field">
                    <label class="modal-label" for="edit_hp_nasabah">No. HP</label>
                    <input id="edit_hp_nasabah" type="text" class="modal-input" placeholder="Kosongkan jika tidak diubah">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-modal btn-modal-cancel" id="modal-edit-btn-cancel">
                        Batal
                    </button>
                    <button type="submit" class="btn-modal btn-modal-save" id="modal-edit-btn-save">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS NASABAH -->
    <div id="modal-hapus-nasabah" class="modal-backdrop hidden">
        <div class="modal-card modal-small">
            <div class="modal-header">
                <div class="modal-title">Hapus Data Nasabah</div>
            </div>

            <div class="delete-message">
                Apakah Anda yakin ingin menghapus data nasabah?
            </div>

            <div class="modal-actions" style="justify-content: center; margin-top: 20px;">
                <button type="button" class="btn-modal btn-modal-secondary" id="modal-hapus-btn-batal">
                    Batal
                </button>
                <button type="button" class="btn-modal btn-modal-cancel" id="modal-hapus-btn-hapus">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        // Kelola Nasabah Functions
        (function () {
            var data = []; // array sementara untuk menampung data nasabah
            var editingIndex = null;
            var deletingIndex = null;

            var rowsEl = document.getElementById('kelola-table-rows');
            var emptyEl = document.getElementById('kelola-table-empty');
            var tableBody = document.getElementById('kelola-table-body');

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
                            data = response.data.map(function(nasabah) {
                                return {
                                    id: nasabah.id,
                                    nama: nasabah.name,
                                    email: nasabah.email,
                                    saldo: parseInt(nasabah.saldo) || 0,
                                    alamat: nasabah.alamat || '',
                                    hp: nasabah.no_hp || ''
                                };
                            });
                            renderTable();
                        } else {
                            throw new Error(response.message || 'Failed to load data');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading nasabah data:', error);
                        showNotification('Gagal memuat data nasabah: ' + error.message, 'error');
                        // Tampilkan data kosong jika gagal
                        data = [];
                        renderTable();
                    });
            }

            function renderTable() {
                if (!rowsEl || !emptyEl) return;

                rowsEl.innerHTML = '';

                if (!data.length) {
                    emptyEl.classList.remove('hidden');
                    return;
                }

                emptyEl.classList.add('hidden');

                data.forEach(function (item, index) {
                    var row = document.createElement('div');
                    row.className = 'kelola-row';
                    row.innerHTML =
                        '<div class="kelola-cell">' + (item.nama || '') + '</div>' +
                        '<div class="kelola-cell">' + (item.alamat || '-') + '</div>' +
                        '<div class="kelola-cell">' + (item.hp || '-') + '</div>' +
                        '<div class="kelola-cell">Rp ' + (item.saldo || 0).toLocaleString('id-ID') + '</div>' +
                        '<div class="kelola-cell kelola-actions">' +
                            '<button type="button" class="icon-action icon-edit" data-index="' + index + '">✏️</button>' +
                            '<button type="button" class="icon-action icon-delete" data-index="' + index + '">🗑</button>' +
                        '</div>';
                    rowsEl.appendChild(row);
                });
            }

            function openModal(id) {
                var el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            }
            function closeModal(id) {
                var el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            }

            // Tambah nasabah
            var tambahModalId = 'modal-tambah-nasabah';
            var btnOpenTambah = document.getElementById('btn-open-tambah-nasabah');
            var btnCloseTambah = document.getElementById('modal-close-tambah');
            var btnCancelTambah = document.getElementById('modal-btn-cancel');
            var formTambah = document.getElementById('form-tambah-nasabah');
            var btnSaveTambah = document.getElementById('modal-btn-save');

            if (btnOpenTambah) btnOpenTambah.addEventListener('click', function () {
                openModal(tambahModalId);
            });
            if (btnCloseTambah) btnCloseTambah.addEventListener('click', function () {
                closeModal(tambahModalId);
            });
            if (btnCancelTambah) btnCancelTambah.addEventListener('click', function () {
                closeModal(tambahModalId);
            });
            if (formTambah) {

                formTambah.addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    if (!btnSaveTambah) return;
                    
                    var nama = document.getElementById('nama_nasabah').value.trim();
                    var alamat = document.getElementById('alamat_nasabah').value.trim();
                    var hp = document.getElementById('hp_nasabah').value.trim();

                    if (!nama) {
                        showNotification('Nama wajib diisi', 'error');
                        return;
                    }

                    // Disable button while saving
                    btnSaveTambah.disabled = true;
                    btnSaveTambah.textContent = 'Menyimpan...';

                    // Call API untuk tambah nasabah
                    fetch('/api/admin/nasabah', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            name: nama,
                            email: nama.toLowerCase().replace(/\s+/g, '.') + '@bank-sampah.local',
                            alamat: alamat,
                            no_hp: hp,
                            password: 'password123' // Default password
                        })
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.success) {
                            showNotification('Nasabah berhasil ditambahkan', 'success');
                            formTambah.reset();
                            closeModal(tambahModalId);
                            loadNasabahData(); // Reload data from API
                        } else {
                            throw new Error(response.message || 'Gagal menambahkan nasabah');
                        }
                    })
                    .catch(error => {
                        console.error('Error adding nasabah:', error);
                        showNotification('Gagal menambahkan nasabah: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Re-enable button
                        btnSaveTambah.disabled = false;
                        btnSaveTambah.textContent = 'Tambah Nasabah';
                    });
                });
            }

            // Edit nasabah
            var editModalId = 'modal-edit-nasabah';
            var formEdit = document.getElementById('form-edit-nasabah');
            var btnCloseEdit = document.getElementById('modal-close-edit');
            var btnCancelEdit = document.getElementById('modal-edit-btn-cancel');
            var btnSaveEdit = document.getElementById('modal-edit-btn-save');

            if (btnCloseEdit) btnCloseEdit.addEventListener('click', function () {
                closeModal(editModalId);
            });
            if (btnCancelEdit) btnCancelEdit.addEventListener('click', function () {
                closeModal(editModalId);
            });
            if (formEdit) {
                formEdit.addEventListener('submit', function (e) {
                    e.preventDefault();
                    
                    if (editingIndex === null || data[editingIndex] === undefined) {
                        closeModal(editModalId);
                        return;
                    }
                    
                    if (!btnSaveEdit) return;
                    
                    var nama = document.getElementById('edit_nama_nasabah').value.trim();
                    var alamat = document.getElementById('edit_alamat_nasabah').value.trim();
                    var hp = document.getElementById('edit_hp_nasabah').value.trim();

                    if (!nama) {
                        showNotification('Nama wajib diisi', 'error');
                        return;
                    }

                    // Disable button while saving
                    btnSaveEdit.disabled = true;
                    btnSaveEdit.textContent = 'Menyimpan...';

                    // Call API untuk edit nasabah
                    const nasabahId = data[editingIndex].id;
                    fetch('/api/admin/nasabah/' + nasabahId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            nama_lengkap: nama,
                            email: data[editingIndex].email, // Preserve existing email
                            alamat: alamat,
                            no_hp: hp
                        })
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.success) {
                            showNotification('Data nasabah berhasil diperbarui', 'success');
                            editingIndex = null;
                            closeModal(editModalId);
                            loadNasabahData(); // Reload data from API
                        } else {
                            throw new Error(response.message || 'Gagal memperbarui data nasabah');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating nasabah:', error);
                        showNotification('Gagal memperbarui data nasabah: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Re-enable button
                        btnSaveEdit.disabled = false;
                        btnSaveEdit.textContent = 'Simpan';
                    });
                });
            }

            // Hapus nasabah
            var hapusModalId = 'modal-hapus-nasabah';
            var btnHapusBatal = document.getElementById('modal-hapus-btn-batal');
            var btnHapusConfirm = document.getElementById('modal-hapus-btn-hapus');

            if (btnHapusBatal) btnHapusBatal.addEventListener('click', function () {
                deletingIndex = null;
                closeModal(hapusModalId);
            });
            if (btnHapusConfirm) btnHapusConfirm.addEventListener('click', function () {
                if (deletingIndex === null || data[deletingIndex] === undefined) {
                    closeModal(hapusModalId);
                    return;
                }

                // Disable button while deleting
                btnHapusConfirm.disabled = true;
                btnHapusConfirm.textContent = 'Menghapus...';

                const nasabahId = data[deletingIndex].id;
                
                // Call API untuk hapus nasabah
                fetch('/api/admin/nasabah/' + nasabahId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        showNotification('Data nasabah berhasil dihapus', 'success');
                        deletingIndex = null;
                        closeModal(hapusModalId);
                        loadNasabahData(); // Reload data from API
                    } else {
                        throw new Error(response.message || 'Gagal menghapus data nasabah');
                    }
                })
                .catch(error => {
                    console.error('Error deleting nasabah:', error);
                    showNotification('Gagal menghapus data nasabah: ' + error.message, 'error');
                })
                .finally(() => {
                    // Re-enable button
                    btnHapusConfirm.disabled = false;
                    btnHapusConfirm.textContent = 'Hapus';
                });
            });

            // Delegasi klik ikon edit / delete di tabel
            if (tableBody) {
                tableBody.addEventListener('click', function (e) {
                    var editBtn = e.target.closest('.icon-edit');
                    var deleteBtn = e.target.closest('.icon-delete');

                    if (editBtn) {
                        var idx = parseInt(editBtn.getAttribute('data-index'), 10);
                        if (!isNaN(idx) && data[idx]) {
                            editingIndex = idx;
                            document.getElementById('edit_nama_nasabah').value = data[idx].nama || '';
                            document.getElementById('edit_alamat_nasabah').value = data[idx].alamat || '';
                            document.getElementById('edit_hp_nasabah').value = data[idx].hp || '';
                            openModal(editModalId);
                        }
                        return;
                    }

                    if (deleteBtn) {
                        var dIdx = parseInt(deleteBtn.getAttribute('data-index'), 10);
                        if (!isNaN(dIdx) && data[dIdx]) {
                            deletingIndex = dIdx;
                            openModal(hapusModalId);
                        }
                    }
                });
            }

            // Load data saat halaman dimuat
            loadNasabahData();

            // Search functionality
            var searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    var searchTerm = this.value.toLowerCase();
                    var filteredData = data.filter(function(item) {
                        return item.nama.toLowerCase().includes(searchTerm) ||
                               (item.hp && item.hp.toLowerCase().includes(searchTerm)) ||
                               (item.alamat && item.alamat.toLowerCase().includes(searchTerm));
                    });
                    
                    // Temporarily override data for rendering
                    var originalData = data;
                    data = filteredData;
                    renderTable();
                    data = originalData; // Restore original data
                });
            }
        })();
    </script>
</body>
</html>
