/**
 * Global Toast Notification System - SIMPLIFIED VERSION
 * Hiển thị thông báo đẹp mắt cho tất cả các hành động trong hệ thống
 */

(function () {
    'use strict';

    // Hàm hiển thị toast notification
    window.showToast = function (message, type) {
        type = type || 'info';

        // Remove existing toasts
        var existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(function (toast) {
            toast.remove();
        });

        // Create toast element
        var toast = document.createElement('div');
        toast.className = 'toast-notification toast-' + type;

        var icon = getToastIcon(type);
        var color = getToastColor(type);

        toast.innerHTML = '<div class="toast-content"><i class="fas fa-' + icon + '"></i><span>' + message + '</span></div>';

        // Add styles
        toast.style.cssText = 'position:fixed;bottom:2rem;right:2rem;background:' + color + ';color:white;padding:1rem 1.5rem;border-radius:0.75rem;box-shadow:0 8px 24px rgba(0,0,0,0.15);z-index:10000;animation:slideInUp 0.3s ease;font-weight:500;max-width:400px;';

        // Add to page
        document.body.appendChild(toast);

        // Auto remove after 4 seconds
        setTimeout(function () {
            toast.style.animation = 'slideOutDown 0.3s ease';
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 4000);
    };

    function getToastIcon(type) {
        var icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    function getToastColor(type) {
        var colors = {
            success: '#27AE60',
            error: '#E74C3C',
            warning: '#F39C12',
            info: '#3498DB'
        };
        return colors[type] || '#3498DB';
    }

    // Add CSS animations
    var style = document.createElement('style');
    style.textContent = '@keyframes slideInUp{from{transform:translateY(100%);opacity:0}to{transform:translateY(0);opacity:1}}@keyframes slideOutDown{from{transform:translateY(0);opacity:1}to{transform:translateY(100%);opacity:0}}.toast-content{display:flex;align-items:center;gap:0.75rem}.toast-content i{font-size:1.2rem}@media (max-width:768px){.toast-notification{bottom:1rem!important;right:1rem!important;left:1rem!important;max-width:none!important}}';
    document.head.appendChild(style);

    // Auto-show toast from PHP session messages when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkSessionMessages);
    } else {
        checkSessionMessages();
    }

    function checkSessionMessages() {
        // Check for PHP session messages
        var successMsg = document.querySelector('[data-toast-success]');
        var errorMsg = document.querySelector('[data-toast-error]');
        var infoMsg = document.querySelector('[data-toast-info]');
        var warningMsg = document.querySelector('[data-toast-warning]');

        if (successMsg) {
            showToast(successMsg.getAttribute('data-toast-success'), 'success');
            successMsg.remove();
        }
        if (errorMsg) {
            showToast(errorMsg.getAttribute('data-toast-error'), 'error');
            errorMsg.remove();
        }
        if (infoMsg) {
            showToast(infoMsg.getAttribute('data-toast-info'), 'info');
            infoMsg.remove();
        }
        if (warningMsg) {
            showToast(warningMsg.getAttribute('data-toast-warning'), 'warning');
            warningMsg.remove();
        }
    }
})();
