<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Nasabah - Bank Sampah Unit</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: #f7f5f3;
            color: #1f2933;
        }
        .hero-bar {
            background: #8dab92;
            height: 54px;
        }
        .shell {
            max-width: 1150px;
            margin: 0 auto;
            padding: 14px 20px 32px;
        }

        /* NAVIGATION */
        .nav-tabs {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 2px 4px;
            border-bottom: 1px solid #d7dbd4;
            background: #faf8f7;
            font-size: 12px;
        }
        .nav-tabs-left {
            display: flex;
            gap: 18px;
        }
        .nav-tabs-right {
            display: flex;
            gap: 12px;
        }
        .nav-tabs a {
            text-decoration: none;
            font-weight: 700;
            color: #4b5563;
            padding: 4px 2px;
        }
        .nav-tabs a.active {
            border-bottom: 2px solid #0f6b2f;
            color: #0f6b2f;
        }

        .tab-content {
            margin-top: 12px;
        }

        .hidden {
            display: none !important;
        }

        /* DASHBOARD SECTION */
        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 10px;
        }
        .card {
            background: #fff;
            border: 1px solid #dcded7;
            border-radius: 6px;
            padding: 12px 14px;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .card-wide {
            grid-column: 1 / -1;
            min-height: 140px;
        }
        .card h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: 800;
            color: #2c302b;
        }
        .value {
            font-size: 30px;
            font-weight: 800;
            margin: 0;
            color: #111827;
        }
        .badge {
            position: absolute;
            top: 10px;
            right: 12px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #0f6b2f;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .panel {
            background: #fff;
            border: 1px solid #dcded7;
            border-radius: 6px;
            margin-top: 12px;
            padding: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            min-height: 220px;
        }
        .panel h4 {
            margin: 0 0 8px 0;
            font-size: 13px;
            font-weight: 800;
            color: #2c302b;
        }
        .empty {
            padding: 36px 8px;
            color: #6b7280;
            font-size: 13px;
            text-align: center;
        }

        /* RIWAYAT SETORAN SECTION */
        .history-header {
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 4px;
            color: #2c302b;
        }
        .history-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .history-box {
            border: 1px solid #dcded7;
            border-radius: 4px;
            min-height: 260px;
            background: #ffffff;
        }

        /* PROFIL SAYA SECTION */
        .profile-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }
        .profile-card {
            width: 360px;
            background: #ffffff;
            border: 1px solid #dcded7;
            border-radius: 6px;
            padding: 24px 20px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .profile-title {
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 6px;
            color: #2c302b;
        }
        .profile-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 18px;
        }
        .profile-avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #0f6b2f;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 32px;
            margin: 0 auto 18px;
        }
        .profile-field-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .profile-field-box {
            border-radius: 3px;
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            height: 32px;
            margin-bottom: 10px;
            width: 100%;
            display: block;
            padding: 6px 10px;
            font-size: 12px;
            color: #374151;
            outline: none;
        }
        .profile-field-box:disabled {
            color: transparent;
            text-shadow: 0 0 0 #9ca3af;
        }
        .profile-field-box.profile-address {
            height: 80px;
            resize: none;
        }
        .profile-button-edit {
            display: block;
            width: 120px;
            margin: 16px auto 0;
            padding: 7px 0;
            border-radius: 4px;
            background: #0f6b2f;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .profile-button-edit:hover {
            background: #0b5224;
        }

        .profile-password-note {
            font-size: 10px;
            color: #6b7280;
            margin-top: -4px;
            margin-bottom: 4px;
        }

        .profile-actions {
            display: flex;
            justify-content: flex-end;
            gap: 14px;
            margin-top: 24px;
        }
        .btn-profile {
            min-width: 120px;
            padding: 7px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            color: #ffffff;
        }
        .btn-cancel {
            background: #dc2626;
        }
        .btn-cancel:hover {
            background: #b91c1c;
        }
        .btn-save {
            background: #1d4ed8;
        }
        .btn-save:hover {
            background: #1e40af;
        }

        @media (max-width: 900px) {
            .cards { grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); }
        }
    </style>
</head>
<body>
    <div class="hero-bar"></div>
    <div class="shell">
        <div class="nav-tabs">
            <div class="nav-tabs-left">
                <a href="javascript:void(0)" class="tab-link active" data-tab="dashboard">Dashboard</a>
                <a href="javascript:void(0)" class="tab-link" data-tab="history">Riwayat Setoran</a>
                <a href="javascript:void(0)" class="tab-link" data-tab="profile">Profil Saya</a>
            </div>
            <div class="nav-tabs-right">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn" style="background: none; border: none; color: #dc2626; font-weight: 700; font-size: 12px; cursor: pointer; padding: 4px 2px;">Keluar</button>
                </form>
            </div>
        </div>

        <div class="tab-content">
            <!-- DASHBOARD -->
            <div id="tab-dashboard">
                <div class="cards">
                    <div class="card card-wide">
                        <span class="badge">📈</span>
                        <h4>Total Saldo Anda</h4>
                        <p class="value" id="total-saldo">Rp 0</p>
                    </div>
                    <div class="card">
                        <span class="badge">♻️</span>
                        <h4>Total Setoran</h4>
                        <p class="value" id="total-setoran">0</p>
                    </div>
                    <div class="card">
                        <span class="badge">⚖️</span>
                        <h4>Total Berat</h4>
                        <p class="value" id="total-berat">0.0 Kg</p>
                    </div>
                    <div class="card">
                        <span class="badge">🏦</span>
                        <h4>Setoran Bulan Ini</h4>
                        <p class="value" id="setoran-bulan-ini">0</p>
                    </div>
                </div>

                <div class="panel">
                    <h4>Setoran Terbaru</h4>
                    <div class="empty">Belum ada data setoran.</div>
                </div>
            </div>

            <!-- RIWAYAT SETORAN -->
            <div id="tab-history" class="hidden">
                <div class="panel">
                    <div class="history-header">Riwayat Setoran Sampah</div>
                    <div class="history-subtitle">Lihat semua transaksi setoran sampah Anda</div>
                    <div class="history-box" id="history-container">
                        <div style="padding: 16px; text-align: center; color: #6b7280;">Memuat data...</div>
                    </div>
                </div>
            </div>

            <!-- PROFIL SAYA -->
            <div id="tab-profile" class="hidden">
                <div class="panel">
                    <div class="profile-title">Profil Saya</div>
                    <div class="profile-subtitle">Kelola informasi profil Anda</div>

                    <div class="profile-wrapper">
                        <div class="profile-card">
                            <div class="profile-avatar">
                                <span>👤</span>
                            </div>

                            <form id="profile-form">
                                <div class="profile-field">
                                    <div class="profile-field-label">Nama Lengkap</div>
                                    <input
                                        type="text"
                                        class="profile-field-box"
                                        name="name"
                                        value=""
                                        disabled
                                    >
                                </div>

                                <div class="profile-field">
                                    <div class="profile-field-label">Alamat</div>
                                    <textarea
                                        class="profile-field-box profile-address"
                                        name="address"
                                        disabled
                                    ></textarea>
                                </div>

                                <div class="profile-field">
                                    <div class="profile-field-label">No. HP</div>
                                    <input
                                        type="text"
                                        class="profile-field-box"
                                        name="phone"
                                        value=""
                                        disabled
                                    >
                                </div>

                                <div class="profile-field">
                                    <div class="profile-field-label">Password</div>
                                    <div class="profile-password-note">Kosongkan jika tidak diubah</div>
                                    <input
                                        type="password"
                                        class="profile-field-box"
                                        name="password"
                                        value=""
                                        disabled
                                    >
                                </div>
                            </form>

                            <div class="profile-actions hidden" id="profile-actions">
                                <button type="button" class="btn-profile btn-cancel" id="btn-cancel-profile">
                                    Batal
                                </button>
                                <button type="button" class="btn-profile btn-save" id="btn-save-profile">
                                    Simpan Perubahan
                                </button>
                            </div>

                            <button type="button" class="profile-button-edit" id="btn-edit-profile">
                                Edit Profil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lastUpdateTimestamp = 0;
        let autoRefreshInterval;

        // Load dashboard data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            startAutoRefresh();
        });

        // Start auto-refresh setiap 5 detik
        function startAutoRefresh() {
            autoRefreshInterval = setInterval(function() {
                checkForUpdates();
            }, 5000); // Check every 5 seconds
        }

        // Stop auto-refresh
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }

        // Function untuk load dashboard data
        function loadDashboardData() {
            fetch('/api/nasabah/dashboard-data', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardCards(data.data);
                    updateSetoranTerbaru(data.data.setoranTerbaru);
                    lastUpdateTimestamp = data.data.lastUpdated;
                } else {
                    console.error('Error loading dashboard data:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Function untuk check update data
        function checkForUpdates() {
            fetch('/api/nasabah/dashboard-data', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Check if data has been updated
                    if (data.data.lastUpdated > lastUpdateTimestamp) {
                        console.log('Data updated, refreshing dashboard...');
                        updateDashboardCards(data.data);
                        updateSetoranTerbaru(data.data.setoranTerbaru);
                        lastUpdateTimestamp = data.data.lastUpdated;

                        // Refresh history if history tab is active
                        refreshHistoryIfActive();

                        // Show update notification
                        showUpdateNotification();
                    }
                }
            })
            .catch(error => {
                console.error('Error checking for updates:', error);
            });
        }

        // Function untuk show notification update
        function showUpdateNotification() {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 12px 16px;
                border-radius: 8px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                font-size: 14px;
                font-weight: 500;
                animation: slideIn 0.3s ease-out;
            `;
            notification.innerHTML = '📊 Data dashboard telah diperbarui!';

            // Add slide-in animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideIn 0.3s ease-in reverse';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Update dashboard cards dengan data dinamis
        function updateDashboardCards(data) {
            document.getElementById('total-saldo').textContent = 'Rp ' + data.totalSaldo.toLocaleString('id-ID');
            document.getElementById('total-setoran').textContent = data.totalSetoran.toLocaleString('id-ID');
            document.getElementById('total-berat').textContent = data.totalBerat.toFixed(2) + ' Kg';
            document.getElementById('setoran-bulan-ini').textContent = data.setoranBulanIni.toLocaleString('id-ID');
        }

        // Update section setoran terbaru
        function updateSetoranTerbaru(setoranTerbaru) {
            const container = document.getElementById('setoran-terbaru');
            if (!container) return;

            if (setoranTerbaru && setoranTerbaru.length > 0) {
                let html = '';
                setoranTerbaru.forEach(setoran => {
                    html += `
                        <div class="setoran-item" style="padding: 8px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; font-size: 12px;">${setoran.keterangan}</div>
                                <div style="font-size: 11px; color: #6b7280;">${setoran.tanggal} ${setoran.waktu}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; font-size: 12px;">Rp ${setoran.jumlah.toLocaleString('id-ID')}</div>
                                <div style="font-size: 11px; color: #6b7280;">0.0 Kg</div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty">Belum ada data setoran.</div>';
            }
        }

        // Ganti konten ketika tab diklik
        document.querySelectorAll('.tab-link').forEach(function (link) {
            link.addEventListener('click', function () {
                var target = this.getAttribute('data-tab');

                // ubah active pada menu
                document.querySelectorAll('.tab-link').forEach(function (l) {
                    l.classList.toggle('active', l === link);
                });

                // tampilkan / sembunyikan konten
                document.querySelectorAll('[id^="tab-"]').forEach(function (section) {
                    section.classList.add('hidden');
                });
                var activeSection = document.getElementById('tab-' + target);
                if (activeSection) {
                    activeSection.classList.remove('hidden');

                    // Load history data jika tab history diklik
                    if (target === 'history') {
                        loadSetoranHistory();
                    }

                    // Load profile data jika tab profile diklik
                    if (target === 'profile') {
                        loadProfileData();
                    }

                    // Control auto-refresh based on active tab
                    if (target === 'dashboard') {
                        startAutoRefresh();
                    } else {
                        stopAutoRefresh();
                    }
                }
            });
        });

        // Load riwayat setoran
        function loadSetoranHistory() {
            const container = document.getElementById('history-container');
            container.innerHTML = '<div style="padding: 16px; text-align: center; color: #6b7280;">Memuat data...</div>';

            fetch('/api/nasabah/setoran-history', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displaySetoranHistory(data.data);
                } else {
                    container.innerHTML = '<div style="padding: 16px; text-align: center; color: #dc2626;">Gagal memuat data riwayat.</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div style="padding: 16px; text-align: center; color: #dc2626;">Terjadi kesalahan saat memuat data.</div>';
            });
        }

        // Load profile data
        function loadProfileData() {
            fetch('/api/nasabah/profile-data', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateProfileForm(data.data);
                } else {
                    console.error('Error loading profile data:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Update profile form dengan data dinamis
        function updateProfileForm(data) {
            document.querySelector('input[name="name"]').value = data.name || '';
            document.querySelector('textarea[name="address"]').value = data.alamat || '';
            document.querySelector('input[name="phone"]').value = data.no_hp || '';

            // Update original values for cancel functionality
            document.querySelector('input[name="name"]').dataset.original = data.name || '';
            document.querySelector('textarea[name="address"]').dataset.original = data.alamat || '';
            document.querySelector('input[name="phone"]').dataset.original = data.no_hp || '';
        }

        // Display riwayat setoran
        function displaySetoranHistory(historyData) {
            const container = document.getElementById('history-container');

            if (historyData && historyData.length > 0) {
                let html = '';
                historyData.forEach(item => {
                    html += `
                        <div style="padding: 12px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; font-size: 13px;">${item.keterangan}</div>
                                <div style="font-size: 11px; color: #6b7280;">${item.tanggal} ${item.waktu}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; font-size: 13px; color: #059669;">Rp ${item.jumlah}</div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div style="padding: 16px; text-align: center; color: #6b7280;">Belum ada riwayat setoran.</div>';
            }
        }

        // Mode edit profil
        (function () {
            var editBtn = document.getElementById('btn-edit-profile');
            var actions = document.getElementById('profile-actions');
            var cancelBtn = document.getElementById('btn-cancel-profile');
            var saveBtn = document.getElementById('btn-save-profile');
            var inputs = document.querySelectorAll('#profile-form input, #profile-form textarea');

            if (!editBtn) return;

            // simpan nilai awal
            inputs.forEach(function (el) {
                el.dataset.original = el.value || '';
            });

            editBtn.addEventListener('click', function () {
                inputs.forEach(function (el) {
                    el.disabled = false;
                });
                editBtn.classList.add('hidden');
                actions.classList.remove('hidden');
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    inputs.forEach(function (el) {
                        el.value = el.dataset.original || '';
                        el.disabled = true;
                    });
                    actions.classList.add('hidden');
                    editBtn.classList.remove('hidden');
                });
            }

            if (saveBtn) {
                // sementara hanya mengunci kembali form, backend bisa ditambahkan sendiri
                saveBtn.addEventListener('click', function () {
                    inputs.forEach(function (el) {
                        el.disabled = true;
                        el.dataset.original = el.value || '';
                    });
                    actions.classList.add('hidden');
                    editBtn.classList.remove('hidden');
                });
            }
        })();
    </script>
</body>
</html>
