 <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Admin - Bank Sampah Unit</title>
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

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fdf9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            text-align: center;
            min-height: 120px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border-color: #0f6b2f;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0f6b2f, #16a34a);
        }
        .card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f6b2f, #16a34a);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(15, 107, 47, 0.3);
            margin-bottom: 4px;
        }
        .card .meta { display: flex; flex-direction: column; gap: 4px; width: 100%; }
        .card .label { 
            font-size: 13px; 
            color: #64748b; 
            font-weight: 600; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .card .value { 
            font-size: 18px; 
            font-weight: 800; 
            color: #1e293b; 
            align-self: flex-start;
            line-height: 1.2;
        }
        .panel {
            background: #fff;
            border: 1px solid #dbe7df;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
            min-height: 420px;
        }
        .panel-header {
            font-weight: 800;
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #1f2c3a;
        }
        .panel-body {
            border-top: 1px solid #e5e7eb;
            margin-top: 6px;
            height: calc(100% - 22px);
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

        .hidden {
            display: none !important;
        }

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

        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { flex-direction: row; flex-wrap: wrap; }
            .setoran-layout { grid-template-columns: 1fr; }
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
                <a href="javascript:void(0)" class="menu-link active" data-section="dashboard"><span class="icon dashboard">▦</span>Dashboard</a>
                <a href="javascript:void(0)" class="menu-link" data-section="kelola-nasabah"><span class="icon users">○</span>Kelola Nasabah</a>
                <a href="javascript:void(0)" class="menu-link" data-section="input-setoran"><span class="icon plus">✚</span>Input Setoran</a>
                <a href="javascript:void(0)" class="menu-link" data-section="laporan"><span class="icon chart">▥</span>Laporan</a>
            </nav>
            
            <!-- Logout Button -->
            <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn" style="background: none; border: none; color: #dc2626; font-weight: 700; font-size: 12px; cursor: pointer; padding: 4px 2px; width: 100%; text-align: left; display: flex; align-items: center; gap: 12px;">Keluar</button>
                </form>
            </div>
            <a class="back-link" href="/">&lt; Kembali ke Beranda</a>
        </aside>
        <main class="content">
            <!-- DASHBOARD SECTION -->
            <section id="section-dashboard">
                <div class="page-title">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h1>Dashboard Admin</h1>
                            <p>Manajemen data nasabah Bank Sampah</p>
                        </div>
                        <button type="button" id="btn-refresh-dashboard" class="btn-modal btn-modal-secondary" style="padding: 8px 16px; min-width: auto; font-size: 12px;">
                            <i class="fas fa-sync-alt" id="refresh-icon"></i> Refresh
                        </button>
                    </div>
                    <div id="dashboard-status" style="margin-top: 8px; font-size: 11px; color: #6b7280;"></div>
                </div>




                <div class="cards">
                    <div class="card">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <div class="meta">
                            <span class="label">Total Nasabah</span>
                            <span class="value" id="total-nasabah">{{ number_format($totalNasabah ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>


                    <div class="card">
                        <span class="icon"><i class="fas fa-recycle"></i></span>
                        <div class="meta">
                            <span class="label">Total Setoran (Kg)</span>
                            <span class="value" id="total-setoran">{{ number_format($totalSetoran ?? 0, 2, ',', '.') }} Kg</span>
                        </div>
                    </div>

                    <div class="card">
                        <span class="icon"><i class="fas fa-balance-scale"></i></span>
                        <div class="meta">
                            <span class="label">Total Saldo Keseluruhan</span>
                            <span class="value" id="total-saldo">Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <span class="icon"><i class="fas fa-university"></i></span>
                        <div class="meta">
                            <span class="label">Setoran Bulan Ini</span>
                            <span class="value" id="setoran-bulan-ini">Rp {{ number_format($setoranBulanIni ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>





                <div class="panel">
                    <div class="panel-header">Setoran Terbaru</div>
                    <div class="panel-body">
                        @if(isset($setoranTerbaru) && $setoranTerbaru->count() > 0)
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($setoranTerbaru as $setoran)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #0f6b2f;">
                                        <div>
                                            <div style="font-weight: 700; font-size: 13px; color: #1f2c3a;">
                                                {{ $setoran->user->name ?? 'Nasabah' }}
                                            </div>
                                            <div style="font-size: 11px; color: #6b7280;">
                                                {{ $setoran->created_at->timezone('Asia/Makassar')->format('d/m/Y H:i') }} WITA
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-weight: 700; font-size: 13px; color: #0f6b2f;">
                                                Rp {{ number_format($setoran->jumlah, 0, ',', '.') }}
                                            </div>
                                            <div style="font-size: 10px; color: #9ca3af;">
                                                {{ $setoran->keterangan ?? 'Setoran sampah' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; color: #9ca3af;">
                                <div style="font-size: 32px; margin-bottom: 8px;">📋</div>
                                <div style="font-size: 13px;">Belum ada data setoran</div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- KELOLA NASABAH SECTION -->
            <section id="section-kelola-nasabah" class="hidden">
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
            </section>

            <!-- INPUT SETORAN SECTION -->
            <section id="section-input-setoran" class="hidden">
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
            </section>

            <!-- LAPORAN SECTION -->
            <section id="section-laporan" class="hidden">
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
            </section>
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

        // Ganti section utama ketika menu samping diklik
        document.querySelectorAll('.menu-link').forEach(function (link) {
            link.addEventListener('click', function () {
                var target = this.getAttribute('data-section');

                document.querySelectorAll('.menu-link').forEach(function (l) {
                    l.classList.toggle('active', l === link);
                });

                document.querySelectorAll('main section[id^="section-"]').forEach(function (section) {
                    section.classList.add('hidden');
                });
                var activeSection = document.getElementById('section-' + target);
                if (activeSection) {
                    activeSection.classList.remove('hidden');
                }

                // Load data when switching to dashboard
                if (target === 'dashboard') {
                    loadDashboardData();
                }
            });
        });



        // ========================
        // Dashboard Dinamis Functions
        // ========================
        function formatRupiah(n) {
            return 'Rp' + (n || 0).toLocaleString('id-ID');
        }

        // Enhanced loading states dengan spinner yang lebih menarik
        function showLoading(element, message = 'Loading...') {
            if (element) {
                element.innerHTML = `
                    <div style="
                        display: flex; 
                        flex-direction: column; 
                        align-items: center; 
                        justify-content: center; 
                        padding: 20px; 
                        color: #9ca3af;
                        min-height: 120px;
                    ">
                        <div style="
                            width: 32px; 
                            height: 32px; 
                            border: 3px solid #e5e7eb; 
                            border-top: 3px solid #0f6b2f; 
                            border-radius: 50%; 
                            animation: spin 1s linear infinite;
                            margin-bottom: 8px;
                        "></div>
                        <div style="font-size: 12px; font-weight: 500;">${message}</div>
                    </div>
                    <style>
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                `;
            }
        }

        function showCardLoading(card) {
            const valueElement = card.querySelector('.value');
            if (valueElement) {
                valueElement.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="
                            width: 16px; 
                            height: 16px; 
                            border: 2px solid #e5e7eb; 
                            border-top: 2px solid #0f6b2f; 
                            border-radius: 50%; 
                            animation: spin 1s linear infinite;
                        "></div>
                        <span style="color: #9ca3af;">Memuat...</span>
                    </div>
                `;
            }
        }

        // Enhanced dashboard status management
        let dashboardStatus = {
            lastUpdate: null,
            isLoading: false,
            retryCount: 0,
            maxRetries: 3
        };

        function updateDashboardStatus(message, type = 'info') {
            const statusEl = document.getElementById('dashboard-status');
            if (!statusEl) return;

            const timestamp = new Date().toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                timeZone: 'Asia/Makassar'
            });

            let icon = 'ℹ️';
            let color = '#6b7280';
            
            switch (type) {
                case 'loading':
                    icon = '🔄';
                    color = '#2563eb';
                    break;
                case 'success':
                    icon = '✅';
                    color = '#16a34a';
                    break;
                case 'error':
                    icon = '❌';
                    color = '#dc2626';
                    break;
                case 'warning':
                    icon = '⚠️';
                    color = '#d97706';
                    break;
            }

            statusEl.innerHTML = `
                <span style="color: ${color}; font-weight: 500;">
                    ${icon} ${message}
                </span>
                ${dashboardStatus.lastUpdate ? `<span style="margin-left: 8px; color: #9ca3af;">• Terakhir: ${timestamp} WITA</span>` : ''}
            `;
        }

        // Enhanced refresh button functionality
        function setupRefreshButton() {
            const refreshBtn = document.getElementById('btn-refresh-dashboard');
            const refreshIcon = document.getElementById('refresh-icon');
            
            if (refreshBtn && refreshIcon) {
                refreshBtn.addEventListener('click', function() {
                    // Add visual feedback
                    refreshIcon.style.animation = 'spin 1s linear infinite';
                    refreshBtn.disabled = true;
                    refreshBtn.style.opacity = '0.7';
                    
                    updateDashboardStatus('Memuat ulang data...', 'loading');
                    
                    // Perform refresh
                    loadDashboardData();
                    
                    // Reset button state after a delay
                    setTimeout(() => {
                        refreshIcon.style.animation = '';
                        refreshBtn.disabled = false;
                        refreshBtn.style.opacity = '1';
                    }, 1000);
                });
            }
        }

        // Enhanced error handling dengan retry mechanism
        function handleDashboardError(error, element) {
            console.error('Dashboard Error:', error);
            
            dashboardStatus.retryCount++;
            dashboardStatus.isLoading = false;
            
            const errorMsg = error.message || 'Terjadi kesalahan';
            
            // Show error in all dashboard cards
            const cards = document.querySelectorAll('#section-dashboard .card .value');
            cards.forEach(card => {
                card.innerHTML = `
                    <div style="
                        color: #dc2626; 
                        font-size: 11px; 
                        font-weight: 600;
                        text-align: center;
                        padding: 8px;
                        background: #fef2f2;
                        border-radius: 4px;
                        border: 1px solid #fecaca;
                    ">
                        ❌ Error: ${errorMsg}
                    </div>
                `;
            });

            // Update status with retry option
            if (dashboardStatus.retryCount < dashboardStatus.maxRetries) {
                updateDashboardStatus(
                    `Error: ${errorMsg}. Akan mencoba lagi dalam 5 detik... (${dashboardStatus.retryCount}/${dashboardStatus.maxRetries})`,
                    'error'
                );
                
                // Auto retry
                setTimeout(() => {
                    const dashboardSection = document.getElementById('section-dashboard');
                    if (dashboardSection && !dashboardSection.classList.contains('hidden')) {
                        loadDashboardData();
                    }
                }, 5000);
            } else {
                updateDashboardStatus(
                    `Gagal memuat data setelah ${dashboardStatus.maxRetries} percobaan. Klik tombol Refresh untuk mencoba lagi.`,
                    'error'
                );
                
                // Show retry button
                const refreshBtn = document.getElementById('btn-refresh-dashboard');
                if (refreshBtn) {
                    refreshBtn.innerHTML = '<i class="fas fa-redo"></i> Coba Lagi';
                    refreshBtn.style.backgroundColor = '#dc2626';
                }
            }
        }

        // Fungsi untuk menampilkan notifikasi yang lebih baik
        function showNotification(message, type = 'info') {
            // Buat elemen notifikasi
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

            // Set warna berdasarkan type
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

            // Add CSS animation
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

            // Animasi keluar
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                notification.style.transition = 'all 0.3s ease';
            }, 3000);

            // Hapus setelah animasi
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3300);
        }



        function loadDashboardData() {
            // Show loading states
            const setoranEl = document.getElementById('total-setoran');
            const saldoEl = document.getElementById('total-saldo');
            const bulanIniEl = document.getElementById('setoran-bulan-ini');
            const nasabahEl = document.getElementById('total-nasabah');
            
            if (setoranEl) showLoading(setoranEl);
            if (saldoEl) showLoading(saldoEl);
            if (bulanIniEl) showLoading(bulanIniEl);
            if (nasabahEl) showLoading(nasabahEl);

            fetch('/api/admin/dashboard-data')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data) {
                        updateDashboardCards(data.data);
                        updateSetoranTerbaru(data.data.setoranTerbaru || []);
                        
                        // Trigger custom event untuk notify bagian lain
                        document.dispatchEvent(new CustomEvent('dashboardDataUpdated', {
                            detail: data.data
                        }));
                    } else {
                        throw new Error('Data tidak valid: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error loading dashboard data:', error);
                    
                    // Reset to show last known values atau show error
                    const errorMsg = '❌ Gagal memuat data';
                    if (setoranEl) setoranEl.textContent = errorMsg;
                    if (saldoEl) saldoEl.textContent = errorMsg;
                    if (bulanIniEl) bulanIniEl.textContent = errorMsg;
                    if (nasabahEl) nasabahEl.textContent = '0';
                    
                    // Show notification to user
                    showNotification('Gagal memuat data dashboard. Silakan coba lagi.', 'error');
                    
                    // Auto retry after 5 seconds if dashboard is currently visible
                    setTimeout(() => {
                        const dashboardSection = document.getElementById('section-dashboard');
                        if (dashboardSection && !dashboardSection.classList.contains('hidden')) {
                            loadDashboardData();
                        }
                    }, 5000);
                });
        }



        function updateDashboardCards(data) {
            console.log('🔄 Update Dashboard Cards dengan data:', data);
            
            // Update total setoran (dalam Kg - dari total_berat jika ada, atau konversi dari jumlah)
            const totalSetoranEl = document.getElementById('total-setoran');
            if (totalSetoranEl) {
                // Gunakan data.totalSetoran langsung (ini seharusnya total nilai setoran)
                const totalSetoran = parseInt(data.totalSetoran) || 0;
                totalSetoranEl.textContent = 'Rp ' + totalSetoran.toLocaleString('id-ID');
                console.log('📊 Total Setoran:', totalSetoran);
            }

            // Update total saldo - PERBAIKAN UTAMA
            const totalSaldoEl = document.getElementById('total-saldo');
            if (totalSaldoEl) {
                const totalSaldo = parseInt(data.totalSaldo) || 0;
                totalSaldoEl.textContent = 'Rp ' + totalSaldo.toLocaleString('id-ID');
                console.log('💰 Total Saldo:', totalSaldo);
                
                // Tambahkan debug info
                totalSaldoEl.title = `Saldo Total: Rp ${totalSaldo.toLocaleString('id-ID')} (raw: ${data.totalSaldo})`;
            }

            // Update setoran bulan ini
            const setoranBulanIniEl = document.getElementById('setoran-bulan-ini');
            if (setoranBulanIniEl) {
                const setoranBulan = parseInt(data.setoranBulanIni) || 0;
                setoranBulanIniEl.textContent = 'Rp ' + setoranBulan.toLocaleString('id-ID');
                console.log('📅 Setoran Bulan Ini:', setoranBulan);
            }

            // Update total nasabah count
            const totalNasabahEl = document.getElementById('total-nasabah');
            if (totalNasabahEl) {
                const totalNasabah = parseInt(data.totalNasabah) || 0;
                totalNasabahEl.textContent = totalNasabah.toLocaleString('id-ID');
                console.log('👥 Total Nasabah:', totalNasabah);
            }
            
            console.log('✅ Dashboard cards updated successfully');
        }

        function updateSetoranTerbaru(setoranTerbaru) {
            const panelBody = document.querySelector('#section-dashboard .panel-body');
            if (!panelBody) return;

            if (!setoranTerbaru || setoranTerbaru.length === 0) {
                panelBody.innerHTML = `
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; color: #9ca3af;">
                        <div style="font-size: 32px; margin-bottom: 8px;">📋</div>
                        <div style="font-size: 13px;">Belum ada data setoran</div>
                    </div>
                `;
                return;
            }

            let html = '<div style="display: flex; flex-direction: column; gap: 8px;">';
            setoranTerbaru.forEach(function(setoran) {
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #0f6b2f;">
                        <div>
                            <div style="font-weight: 700; font-size: 13px; color: #1f2c3a;">
                                ${setoran.nama}
                            </div>
                            <div style="font-size: 11px; color: #6b7280;">
                                ${setoran.tanggal} ${setoran.waktu}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; font-size: 13px; color: #0f6b2f;">
                                ${formatRupiah(setoran.jumlah)}
                            </div>
                            <div style="font-size: 10px; color: #9ca3af;">
                                ${setoran.keterangan}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            panelBody.innerHTML = html;
        }



        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            
            // Auto refresh setiap 30 detik
            setInterval(function() {
                const currentSection = document.querySelector('main section:not(.hidden)');
                if (currentSection && currentSection.id === 'section-dashboard') {
                    loadDashboardData();
                }
            }, 30000);
        });


        // Kelola Nasabah (tambah, edit, hapus secara sederhana di sisi front-end)
        (function () {
            var data = []; // array sementara untuk menampung data nasabah
            var editingIndex = null;
            var deletingIndex = null;

            var rowsEl = document.getElementById('kelola-table-rows');
            var emptyEl = document.getElementById('kelola-table-empty');
            var tableBody = document.getElementById('kelola-table-body');

            // Function untuk mengisi dropdown setoran dengan data nasabah
            function populateSetoranDropdown(nasabahData) {
                const dropdown = document.getElementById('setoran-nasabah');
                if (!dropdown) return;

                // Clear existing options (except the first one)
                dropdown.innerHTML = '<option value="">-- Pilih Nasabah --</option>';

                // Add all nasabah to dropdown
                if (nasabahData && Array.isArray(nasabahData)) {
                    nasabahData.forEach(function(nasabah) {
                        const option = document.createElement('option');
                        option.value = nasabah.id;
                        option.textContent = `${nasabah.nama} - ${nasabah.hp}`;
                        option.dataset.saldo = nasabah.saldo || 0; // Tambahkan saldo ke dataset
                        dropdown.appendChild(option);
                    });
                }
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
                            populateSetoranDropdown(data); // Isi dropdown setoran dengan data
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
                        populateSetoranDropdown([]);
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

            // Listen untuk event dashboard data updated (jika ada setoran baru)
            document.addEventListener('dashboardDataUpdated', function(event) {
                loadNasabahData(); // Reload data nasabah saat dashboard diupdate
            });

            // Listen untuk event setoran added
            document.addEventListener('setoranAdded', function(event) {
                loadNasabahData(); // Reload data nasabah saat ada setoran baru
            });
        })();


        // Input Setoran Functions (untuk dashboard)
        (function () {
            var setoranItems = [];
            var totalBerat = 0;
            var totalNilai = 0;

            function updateSetoranSummary() {
                const totalBeratEl = document.getElementById('setoran-total-berat');
                const totalNilaiEl = document.getElementById('setoran-total-nilai');
                const saldoSetelahEl = document.getElementById('setoran-saldo-setelah');
                
                if (totalBeratEl) totalBeratEl.textContent = totalBerat.toFixed(2) + ' Kg';
                if (totalNilaiEl) totalNilaiEl.textContent = formatRupiah(totalNilai);
                
                // Update saldo setelah transaksi (dari dropdown nasabah)
                const nasabahSelect = document.getElementById('setoran-nasabah');
                if (nasabahSelect && saldoSetelahEl) {
                    const selectedOption = nasabahSelect.selectedOptions[0];
                    if (selectedOption && selectedOption.dataset && selectedOption.dataset.saldo) {
                        const saldoAwal = parseInt(selectedOption.dataset.saldo) || 0;
                        const saldoSekarang = saldoAwal + totalNilai;
                        saldoSetelahEl.textContent = formatRupiah(saldoSekarang);
                        
                        // Debug: Log informasi saldo untuk debugging
                        console.log('Saldo awal:', saldoAwal);
                        console.log('Total nilai transaksi:', totalNilai);
                        console.log('Saldo setelah transaksi:', saldoSekarang);
                    } else {
                        saldoSetelahEl.textContent = formatRupiah(totalNilai);
                        console.log('Saldo tidak ditemukan di dataset, menampilkan total nilai saja');
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

            // Make remove function globally available
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
                            
                            renderSetoranItems();
                            
                            // Reload dashboard data
                            loadDashboardData();
                            
                            // Trigger custom event
                            document.dispatchEvent(new CustomEvent('setoranAdded', {
                                detail: data.data
                            }));
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
                nasabahSelect.addEventListener('change', function() {
                    const saldoAwalEl = document.getElementById('setoran-saldo-awal');
                    if (saldoAwalEl) {
                        const selectedOption = this.selectedOptions[0];
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

            // Update saldo dropdown dengan data dari API
            function updateNasabahSaldoInDropdown() {
                if (typeof data !== 'undefined' && data.length > 0) {
                    const dropdown = document.getElementById('setoran-nasabah');
                    if (dropdown) {
                        // Update each option with saldo data
                        Array.from(dropdown.options).forEach(function(option) {
                            if (option.value) {
                                const nasabah = data.find(function(n) { return n.id == option.value; });
                                if (nasabah) {
                                    option.dataset.saldo = nasabah.saldo || 0;
                                }
                            }
                        });
                    }
                }
            }

            // Listen for data loaded events
            document.addEventListener('dashboardDataUpdated', function(event) {
                updateNasabahSaldoInDropdown();
            });

            document.addEventListener('setoranDataLoaded', function(event) {
                updateNasabahSaldoInDropdown();
            });
        })();

        // LAPORAN (menggunakan data real dari API)
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

            // FIXED: Enhanced export functions dengan endpoint yang benar dan error handling yang lebih baik
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

    <!-- Select2 CSS dan JS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Script untuk menginisialisasi Select2 hanya pada dropdown setoran-nasabah -->
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 hanya untuk dropdown setoran-nasabah
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

            // Event listener untuk handle perubahan pilihan pada dropdown
            $('#setoran-nasabah').on('change', function() {
                const selectedValue = $(this).val();
                if (selectedValue) {
                    // Update saldo awal berdasarkan pilihan nasabah
                    const selectedOption = $(this).find('option:selected');
                    console.log('Selected nasabah ID:', selectedValue);
                    // Di sini bisa ditambahkan logic untuk menampilkan saldo nasabah
                }
            });

            // Pastikan dropdown terisi ulang setelah data dimuat
            $(document).on('setoranDataLoaded', function() {
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
            });
        });
    </script>
</body>
</html>

