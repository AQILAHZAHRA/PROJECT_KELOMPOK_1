import './bootstrap';
import Alpine from 'alpinejs';

// Import global styles
import '../css/app.css';

// Import jQuery for compatibility
window.$ = window.jQuery = require('jquery');

// Import Select2
import 'select2';

// Import DataTables if needed
if (typeof $.fn.DataTable !== 'undefined') {
    require('datatables.net');
}

// Import Chart.js
import Chart from 'chart.js/auto';

// Make Chart available globally
window.Chart = Chart;

// Alpine.js setup
window.Alpine = Alpine;
Alpine.start();

// Global utilities
window.formatRupiah = function(n) {
    return 'Rp' + (n || 0).toLocaleString('id-ID');
};

window.formatTanggal = function(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

window.showNotification = function(message, type = 'info') {
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
};

// Initialize components when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize Select2 for all select elements
    $('select').select2({
        placeholder: '-- Pilih --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada data";
            }
        }
    });

    // Add loading state to buttons
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            }
        });
    });
});

// Export utility functions
window.BankSampah = {
    formatRupiah,
    formatTanggal,
    showNotification,
    utils: {
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        validateEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },
        validatePhone: function(phone) {
            const re = /^08[0-9]{9,12}$/;
            return re.test(phone.replace(/[\s-()]/g, ''));
        }
    }
};
