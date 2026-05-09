/**
 * JS Handler cho User Account (Profile, Bookings, Wishlist)
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Kiểm tra trạng thái đăng nhập
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        window.location.href = window.BASE_URL_FROM_PHP || '/';
        return;
    }

    // 2. Fetch dữ liệu Account
    fetchAccountData(token);
    
    // 3. Khởi tạo form Đổi mật khẩu (form tĩnh trong HTML)
    setupPasswordForm(token);
});

async function fetchAccountData(token) {
    try {
        const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'user/account', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            // Token hết hạn hoặc không hợp lệ — xóa token và redirect về home
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user_data');
            window.location.href = window.BASE_URL_FROM_PHP || '/tour1/';
            return;
        }

        // Kiểm tra Content-Type trước khi parse JSON để tránh crash
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const rawText = await response.text();
            console.error('API returned non-JSON response:', rawText);
            showLoadingError('Lỗi máy chủ: API trả về dữ liệu không hợp lệ. Kiểm tra console để biết chi tiết.');
            return;
        }

        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            renderProfile(data.profile);
            renderBookings(data.bookings);
            renderWishlist(data.wishlist);
        } else {
            showLoadingError('Không thể tải dữ liệu: ' + (result.message || 'Lỗi không xác định'));
        }
    } catch (err) {
        console.error("Fetch error:", err);
        showLoadingError('Lỗi kết nối đến máy chủ. Vui lòng tải lại trang.');
    }
}

/**
 * Hiển thị lỗi tải dữ liệu ra tất cả các tab content
 */
function showLoadingError(message) {
    const ids = ['profile-tab-content', 'bookings-tab-content', 'wishlist-tab-content'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.innerHTML = `<div style="text-align:center; padding: 2rem; color: var(--danger, #ef4444);">
                <i class="fas fa-exclamation-circle" style="font-size:2rem; margin-bottom:0.5rem;"></i>
                <p>${message}</p>
                <button class="btn btn-ghost" onclick="location.reload()" style="margin-top:1rem;">Thử lại</button>
            </div>`;
        }
    });
}


function renderProfile(profile) {
    const container = document.getElementById('profile-tab-content');
    if (!container) return;

    if (!profile) {
        container.innerHTML = '<p>Không tìm thấy thông tin cá nhân.</p>';
        return;
    }

    container.innerHTML = `
        <form name="profileForm" class="form-stack" id="js-profile-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Họ và tên <span class="required">*</span></label>
                    <input type="text" name="name" id="name" value="${profile.FullName || ''}" required>
                </div>
                <div class="form-group">
                    <label for="mobileno">Số điện thoại <span class="required">*</span></label>
                    <input type="text" name="mobileno" id="mobileno" maxlength="10" value="${profile.MobileNumber || ''}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" value="${profile.EmailId || ''}" disabled>
                <p class="helper-text">Email không thể thay đổi vì đây là tài khoản đăng nhập của bạn.</p>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" value="${profile.Address || ''}" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố">
                <p class="helper-text">Địa chỉ để xe đưa đón (nếu có).</p>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="dateofbirth">Ngày sinh</label>
                    <input type="date" name="dateofbirth" id="dateofbirth" value="${profile.DateOfBirth || ''}">
                </div>
                <div class="form-group">
                    <label for="gender">Giới tính</label>
                    <select name="gender" id="gender">
                        <option value="">Chọn giới tính</option>
                        <option value="Nam" ${profile.Gender === 'Nam' ? 'selected' : ''}>Nam</option>
                        <option value="Nữ" ${profile.Gender === 'Nữ' ? 'selected' : ''}>Nữ</option>
                        <option value="Khác" ${profile.Gender === 'Khác' ? 'selected' : ''}>Khác</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="submit" class="btn">
                <i class="fas fa-save"></i> Lưu thay đổi
            </button>
        </form>
    `;

    // Gắn sự kiện Submit cho Profile Form bằng Fetch + JWT
    document.getElementById('js-profile-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        btn.disabled = true;

        const payload = {
            name:        form.querySelector('#name').value.trim(),
            mobileno:    form.querySelector('#mobileno').value.trim(),
            address:     (form.querySelector('#address')?.value || '').trim(),
            dateofbirth: (form.querySelector('#dateofbirth')?.value || '').trim(),
            gender:      (form.querySelector('#gender')?.value || '').trim()
        };

        const token = localStorage.getItem('jwt_token');
        try {
            const res = await fetch((window.BASE_API_URL || '/tour1/api/') + 'user/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                showToast(result.message || 'Hồ sơ đã được cập nhật!', 'success');
            } else {
                showToast(result.message || 'Cập nhật thất bại', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Lỗi kết nối máy chủ', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}

/**
 * Gắn sự kiện Submit cho form Đổi mật khẩu (static HTML trong view)
 */
function setupPasswordForm(token) {
    const form = document.querySelector('form[name="changePasswordForm"]');
    if (!form) return;

    // Chuyển form sang Fetch, không còn action POST truyền thống
    form.removeAttribute('action');
    form.removeAttribute('method');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đổi...';
        btn.disabled = true;

        const payload = {
            password:        form.querySelector('#password').value,
            newpassword:     form.querySelector('#newpassword').value,
            confirmpassword: form.querySelector('#confirmpassword').value
        };

        try {
            const res = await fetch((window.BASE_API_URL || '/tour1/api/') + 'user/password', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                showToast(result.message || 'Đổi mật khẩu thành công!', 'success');
                form.reset();
            } else {
                showToast(result.message || 'Đổi mật khẩu thất bại', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Lỗi kết nối máy chủ', 'error');
        } finally {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    });
}

/**
 * Helper: Hiển thị toast notification
 */
function showToast(message, type = 'success') {
    // Sử dụng hệ thống toast đã có sẵn nếu có, fallback về alert
    if (window.showToastNotification) {
        window.showToastNotification(message, type);
        return;
    }
    // Fallback
    alert(message);
}


function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
}

function renderBookings(bookings) {
    const container = document.getElementById('bookings-tab-content');
    if (!container) return;

    if (!bookings || bookings.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
                <p>Bạn chưa có đặt tour nào.</p>
                <a href="${window.BASE_URL_FROM_PHP}package" class="btn">
                    <i class="fas fa-search"></i> Khám phá tour
                </a>
            </div>
        `;
        return;
    }

    let html = '<div class="bookings-grid">';
    bookings.forEach(booking => {
        let statusText = "Chờ xử lý";
        let statusClass = "is-pending";
        let statusIcon = "clock";
        if (booking.status == 1) {
            statusText = "Đã xác nhận";
            statusClass = "is-approved";
            statusIcon = "check-circle";
        } else if (booking.status == 3) {
            statusText = "Đã hoàn thành";
            statusClass = "is-completed";
            statusIcon = "flag-checkered";
        } else if (booking.status == 2) {
            statusText = "Đã hủy";
            statusClass = "is-cancelled";
            statusIcon = "times-circle";
        }

        let formattedRegDate = booking.regdate;
        try {
            const dateObj = new Date(booking.regdate.replace(/-/g, "/")); // Replace - with / for better browser compatibility
            if (!isNaN(dateObj)) {
                formattedRegDate = dateObj.toLocaleDateString('vi-VN');
            }
        } catch (e) {
            console.error("Date parse error", e);
        }

        html += `
            <div class="booking-card">
                <div class="booking-header">
                    <span class="booking-code">#BK${booking.bookid}</span>
                    <span class="status-chip ${statusClass}">
                        <i class="fas fa-${statusIcon}"></i>
                        ${statusText}
                    </span>
                </div>
                <div class="booking-body">
                    <h4 class="booking-tour-name">
                        <a href="${window.BASE_URL_FROM_PHP}package/details/${booking.pkgid}">
                            ${booking.packagename}
                        </a>
                    </h4>
                    <div class="booking-details">
                        <div class="booking-detail-item">
                            <i class="fas fa-calendar"></i>
                            <span>Ngày khởi hành: ${booking.fromdate}</span>
                        </div>
                        <div class="booking-detail-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <span class="booking-price">${formatCurrency(booking.packageprice)}</span>
                        </div>
                        <div class="booking-detail-item">
                            <i class="fas fa-clock"></i>
                            <span>Đặt ngày ${formattedRegDate}</span>
                        </div>
                    </div>

                    ${booking.admin_notes ? `
                        <div class="admin-message">
                            <div class="admin-message-header">
                                <i class="fas fa-comment-dots admin-message-icon"></i>
                                <span class="admin-message-title">Phản hồi từ Admin</span>
                            </div>
                            <div class="admin-message-content">${booking.admin_notes}</div>
                        </div>
                    ` : ''}

                    ${booking.cancelreason ? `
                        <div class="booking-comment" style="margin-top:0.5rem; border-left-color: var(--danger);">
                            <i class="fas fa-info-circle"></i>
                            <span>Lý do hủy: ${booking.cancelreason}</span>
                        </div>
                    ` : ''}
                </div>
                <div class="booking-footer" style="display:flex; justify-content:space-between; align-items:center;">
                    <a href="${window.BASE_URL_FROM_PHP}package/details/${booking.pkgid}" class="btn btn-ghost btn-compact">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                    ${booking.status == 0 ? `
                        <button class="btn btn-compact" style="background:var(--danger); border-color:var(--danger);" onclick="cancelBooking(${booking.bookid})">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

async function cancelBooking(bookingId) {
    if (!confirm('Bạn có chắc chắn muốn hủy đặt tour này không?')) return;

    const token = localStorage.getItem('jwt_token');
    if (!token) return;

    try {
        const response = await fetch((window.BASE_API_URL || '/tour1/api/') + `user/booking/${bookingId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        const res = await response.json();
        
        if (res.success) {
            alert('Hủy tour thành công');
            fetchAccountData(token); // reload data
        } else {
            alert(res.message || 'Có lỗi xảy ra');
        }
    } catch (error) {
        console.error('Cancel booking error:', error);
        alert('Lỗi kết nối');
    }
}

function renderWishlist(wishlistItems) {
    const container = document.getElementById('wishlist-tab-content');
    if (!container) return;

    if (!wishlistItems || wishlistItems.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-heart-broken" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
                <p>Bạn chưa có tour yêu thích nào.</p>
                <a href="${window.BASE_URL_FROM_PHP}package" class="btn">
                    <i class="fas fa-search"></i> Khám phá tour
                </a>
            </div>
        `;
        return;
    }

    let html = '<div class="tour-grid">';
    wishlistItems.forEach(item => {
        html += `
            <div class="tour-card">
                <div class="badge">${item.PackageType}</div>
                <div class="tour-card__media">
                    <img src="${window.BASE_URL_FROM_PHP}admin/packageimages/${item.PackageImage}" alt="${item.PackageName}">
                    <div class="tour-card__duration">
                        <i class="fas fa-calendar-alt"></i>
                        <span>${item.TourDuration}</span>
                    </div>
                </div>
                <div class="tour-card__content">
                    <div class="tour-card__location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${item.PackageLocation}</span>
                    </div>
                    <h3 class="tour-card__title">${item.PackageName}</h3>
                    <div class="tour-card__footer">
                        <div>
                            <span class="tour-card__price-label">GIÁ TỪ</span>
                            <div class="tour-card__price">${formatCurrency(item.PackagePrice)}</div>
                        </div>
                        <a class="tour-card__btn" href="${window.BASE_URL_FROM_PHP}package/details/${item.PackageId}">
                            Chi tiết
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}
