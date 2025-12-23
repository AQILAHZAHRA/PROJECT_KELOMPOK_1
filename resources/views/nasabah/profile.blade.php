@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <div class="h-16 w-16 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900" id="profile-name">Memuat...</h1>
                    <p class="text-gray-600" id="profile-email">Memuat...</p>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
                <p class="mt-1 text-sm text-gray-600">Informasi akun dan data pribadi Anda</p>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900" id="nama-lengkap">
                            Memuat...
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900" id="email">
                            Memuat...
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
                        <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900" id="no-hp">
                            Memuat...
                        </div>
                    </div>

                    <!-- Saldo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Saldo BSU</label>
                        <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-green-50 text-green-900 font-semibold" id="saldo">
                            Memuat...
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900 min-h-[80px]" id="alamat">
                        Memuat...
                    </div>
                </div>

                <!-- Tanggal Registrasi -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Registrasi</label>
                    <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900" id="tanggal-registrasi">
                        Memuat...
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Setoran -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Setoran</dt>
                                <dd class="text-lg font-medium text-gray-900" id="total-setoran">Memuat...</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Berat -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M12 7l3 9m0 0l6-2"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Berat Sampah</dt>
                                <dd class="text-lg font-medium text-gray-900" id="total-berat">Memuat...</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jumlah Transaksi -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Jumlah Transaksi</dt>
                                <dd class="text-lg font-medium text-gray-900" id="jumlah-transaksi">Memuat...</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard Button -->
        <div class="mt-6">
            <a href="{{ route('nasabah.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<script>
// Load profile data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadProfileData();
});

// Function untuk load profile data
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
            updateProfileData(data.data);
        } else {
            console.error('Error loading profile data:', data.message);
            showErrorMessage('Gagal memuat data profil');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Terjadi kesalahan saat memuat data profil');
    });
}

// Function untuk update profile data
function updateProfileData(data) {
    // Header information
    document.getElementById('profile-name').textContent = data.name;
    document.getElementById('profile-email').textContent = data.email;

    // Profile details
    document.getElementById('nama-lengkap').textContent = data.name;
    document.getElementById('email').textContent = data.email;
    document.getElementById('no-hp').textContent = data.no_hp || '-';
    document.getElementById('alamat').textContent = data.alamat || '-';
    document.getElementById('saldo').textContent = 'Rp ' + data.saldo.toLocaleString('id-ID');
    document.getElementById('tanggal-registrasi').textContent = data.created_at_formatted;

    // Statistics
    document.getElementById('total-setoran').textContent = 'Rp ' + data.total_setoran.toLocaleString('id-ID');
    document.getElementById('total-berat').textContent = data.total_berat.toFixed(2) + ' Kg';
    document.getElementById('jumlah-transaksi').textContent = data.jumlah_transaksi;
}

// Function untuk show error message
function showErrorMessage(message) {
    // Create error notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ef4444;
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        font-size: 14px;
        font-weight: 500;
        animation: slideIn 0.3s ease-out;
    `;
    notification.innerHTML = '❌ ' + message;

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

    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease-in reverse';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}
</script>
@endsection
