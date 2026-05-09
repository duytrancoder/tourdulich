/**
 * Wishlist JavaScript
 * Handles adding/removing tours from wishlist with AJAX
 */

// Use BASE_URL from PHP if available, otherwise fallback to default
const BASE_URL = window.BASE_URL_FROM_PHP || (window.location.origin + '/tour1/');

document.addEventListener('DOMContentLoaded', function () {
    // Initialize wishlist hearts on page load
    initializeWishlistHearts();

    // Add event listeners to all wishlist heart buttons
    const wishlistHearts = document.querySelectorAll('.wishlist-heart');
    wishlistHearts.forEach(heart => {
        heart.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleWishlist(this);
        });
    });
});

/**
 * Initialize wishlist hearts based on current user's wishlist
 */
function initializeWishlistHearts() {
    const hearts = document.querySelectorAll('.wishlist-heart');
    if (hearts.length === 0) return;

    const token = localStorage.getItem('jwt_token');
    if (!token) return; // Không lấy nếu chưa đăng nhập

    fetch((window.BASE_API_URL || '/tour1/api/') + 'user/wishlist', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(res => {
        if (res.success && res.data && res.data.packageIds) {
            hearts.forEach(heart => {
                const packageId = parseInt(heart.dataset.packageId);
                if (res.data.packageIds.includes(packageId)) {
                    heart.classList.add('active');
                }
            });
        }
    })
    .catch(error => console.error('Error fetching wishlist:', error));
}

/**
 * Toggle wishlist status for a package
 */
function toggleWishlist(heartButton) {
    const packageId = heartButton.dataset.packageId;
    if (!packageId) return;

    const token = localStorage.getItem('jwt_token');
    if (!token) {
        const signinModal = document.getElementById('signin-modal');
        if (signinModal) {
            signinModal.classList.add('is-visible');
            showToast('Vui lòng đăng nhập để lưu tour yêu thích', 'warning');
        }
        return;
    }

    // Show loading state
    heartButton.style.pointerEvents = 'none';
    heartButton.style.opacity = '0.6';

    fetch((window.BASE_API_URL || '/tour1/api/') + `user/wishlist/toggle/${packageId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(res => {
        heartButton.style.pointerEvents = '';
        heartButton.style.opacity = '';

        if (res.success) {
            if (res.data && res.data.inWishlist) {
                heartButton.classList.add('active');
                showToast(res.message, 'success');
            } else {
                heartButton.classList.remove('active');
                showToast(res.message, 'info');

                // If on wishlist page, remove the card
                if (window.location.href.includes('account')) {
                    const tourCard = heartButton.closest('.tour-card');
                    if (tourCard) {
                        tourCard.style.transition = 'all 0.3s ease';
                        tourCard.style.opacity = '0';
                        tourCard.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            tourCard.remove();
                            const remainingCards = document.querySelectorAll('.tour-card');
                            if (remainingCards.length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 300);
                    }
                }
            }
        } else {
            showToast(res.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error toggling wishlist:', error);
        heartButton.style.pointerEvents = '';
        heartButton.style.opacity = '';
        showToast('Có lỗi xảy ra. Vui lòng thử lại', 'error');
    });
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
