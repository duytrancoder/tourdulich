/**
 * Wishlist Button JavaScript - For Tour Details Page
 * Handles beautiful wishlist button with clear saved/unsaved states
 */

const BASE_URL = window.location.origin + '/tour1/';

document.addEventListener('DOMContentLoaded', function () {
    const wishlistBtn = document.getElementById('wishlistBtn');

    if (!wishlistBtn) return;

    const packageId = wishlistBtn.dataset.packageId;

    // Initialize button state
    checkWishlistStatus(packageId);

    // Add click handler
    wishlistBtn.addEventListener('click', function () {
        toggleWishlistButton(packageId);
    });
});

/**
 * Check if tour is in wishlist and update button state
 */
function checkWishlistStatus(packageId) {
    const wishlistBtn = document.getElementById('wishlistBtn');

    fetch(BASE_URL + 'wishlist/check/' + packageId, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.inWishlist) {
                updateButtonState(true);
            } else {
                updateButtonState(false);
            }
        })
        .catch(error => {
            console.error('Error checking wishlist:', error);
        });
}

/**
 * Toggle wishlist status
 */
function toggleWishlistButton(packageId) {
    const wishlistBtn = document.getElementById('wishlistBtn');

    // Add loading state
    wishlistBtn.classList.add('loading');

    fetch(BASE_URL + 'wishlist/toggle/' + packageId, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Remove loading state
            wishlistBtn.classList.remove('loading');

            if (data.success) {
                // Update button state
                updateButtonState(data.inWishlist);

                // Show toast notification
                showToast(data.message, data.inWishlist ? 'success' : 'info');
            } else if (data.requireLogin) {
                // User not logged in, show login modal
                const signinModal = document.getElementById('signin-modal');
                if (signinModal) {
                    signinModal.classList.add('is-visible');
                    showToast('Vui lòng đăng nhập để lưu tour yêu thích', 'warning');
                }
            } else {
                showToast('Có lỗi xảy ra. Vui lòng thử lại', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling wishlist:', error);
            wishlistBtn.classList.remove('loading');
            showToast('Có lỗi xảy ra. Vui lòng thử lại', 'error');
        });
}

/**
 * Update button visual state
 */
function updateButtonState(isSaved) {
    const wishlistBtn = document.getElementById('wishlistBtn');
    const wishlistText = wishlistBtn.querySelector('.wishlist-text');

    if (isSaved) {
        wishlistBtn.classList.add('saved');
        wishlistText.textContent = 'Đã lưu vào yêu thích';
    } else {
        wishlistBtn.classList.remove('saved');
        wishlistText.textContent = 'Lưu tour yêu thích';
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${getToastIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;

    // Add styles
    toast.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: ${getToastColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        animation: slideInUp 0.3s ease;
        font-weight: 500;
    `;

    // Add to page
    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutDown 0.3s ease';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

function getToastIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getToastColor(type) {
    const colors = {
        success: '#27AE60',
        error: '#E74C3C',
        warning: '#F39C12',
        info: '#3498DB'
    };
    return colors[type] || '#3498DB';
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .toast-content i {
        font-size: 1.2rem;
    }
`;
document.head.appendChild(style);
