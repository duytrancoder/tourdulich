/**
 * JS Handler cho User Account (Profile, Bookings, Wishlist)
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Kiểm tra trạng thái đăng nhập
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        // Chưa đăng nhập, đá về trang chủ
        window.location.href = window.BASE_URL_FROM_PHP || '/';
        return;
    }

    // 2. Fetch dữ liệu Account
    fetchAccountData(token);
});

async function fetchAccountData(token) {
    try {
        const response = await fetch('/tour1/api/user/account', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user_data');
            window.location.href = '/tour1/';
            return;
        }

        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            renderProfile(data.profile);
            renderBookings(data.bookings);
            renderWishlist(data.wishlist);
        } else {
            alert("Lỗi khi tải thông tin: " + result.message);
        }
    } catch (err) {
        console.error("Fetch error:", err);
        alert("Lỗi kết nối máy chủ");
    }
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

    // Chúng ta sẽ gắn sự kiện Submit Update Profile sau khi API Update được viết (Phase 5)
    document.getElementById('js-profile-form').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Chức năng cập nhật đang được nâng cấp lên REST API!');
    });
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

        const dateObj = new Date(booking.regdate);
        const formattedRegDate = !isNaN(dateObj) ? dateObj.toLocaleDateString('vi-VN') : booking.regdate;

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
                </div>
                <div class="booking-footer">
                    <a href="${window.BASE_URL_FROM_PHP}package/details/${booking.pkgid}" class="btn btn-ghost btn-compact">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
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
