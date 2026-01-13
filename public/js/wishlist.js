/**
 * Wishlist JavaScript
 * Handles adding/removing tours from wishlist with AJAX
 */

const BASE_URL = window.location.origin + '/tour1/';

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
    // Get all package IDs on the current page
    const hearts = document.querySelectorAll('.wishlist-heart');

    if (hearts.length === 0) return;

    // Fetch user's wishlist package IDs
    fetch(BASE_URL + 'wishlist/getIds', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.packageIds && data.packageIds.length > 0) {
                hearts.forEach(heart => {
                    const packageId = parseInt(heart.dataset.packageId);
                    if (data.packageIds.includes(packageId)) {
                        heart.classList.add('active');
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching wishlist:', error);
        });
}

/**
 * Toggle wishlist status for a package
 */
function toggleWishlist(heartButton) {
    const packageId = heartButton.dataset.packageId;

    if (!packageId) {
        console.error('Package ID not found');
        return;
    }

    // Show loading state
    heartButton.style.pointerEvents = 'none';
    heartButton.style.opacity = '0.6';

    // Make AJAX request to toggle wishlist
    fetch(BASE_URL + 'wishlist/toggle/' + packageId, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Remove loading state
            heartButton.style.pointerEvents = '';
            heartButton.style.opacity = '';

            if (data.success) {
                // Update heart state
                if (data.inWishlist) {
                    heartButton.classList.add('active');
                    showToast('Đã thêm vào danh sách yêu thích', 'success');
                } else {
                    heartButton.classList.remove('active');
                    showToast('Đã xóa khỏi danh sách yêu thích', 'info');

                    // If on wishlist page, remove the card
                    if (window.location.href.includes('account')) {
                        const tourCard = heartButton.closest('.tour-card');
                        if (tourCard) {
                            tourCard.style.transition = 'all 0.3s ease';
                            tourCard.style.opacity = '0';
                            tourCard.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                tourCard.remove();

                                // Check if wishlist is now empty
                                const remainingCards = document.querySelectorAll('.tour-card');
                                if (remainingCards.length === 0) {
                                    location.reload(); // Reload to show empty state
                                }
                            }, 300);
                        }
                    }
                }
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
