/**
 * JS Handler cho Admin Manage Packages
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. CREATE PACKAGE (Tạo Tour)
    const packageForm = document.getElementById('packageForm');
    if (packageForm) {
        packageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = packageForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Đang tải lên...';
            submitBtn.disabled = true;

            const formData = new FormData(packageForm);
            
            // Xóa phần thông báo cũ nếu có
            const errorDiv = document.querySelector('.alert.error');
            if(errorDiv) errorDiv.remove();
            
            try {
                // Chúng ta không dùng function apiFetch cũ cho file upload
                // vì apiFetch ép kiểu Content-Type: application/json
                const token = localStorage.getItem('jwt_token');
                if (!token) {
                    alert("Bạn chưa đăng nhập hoặc hết phiên, vui lòng đăng nhập lại.");
                    window.location.href = '../admin/index.php';
                    return;
                }

                const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/tours', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                        // KHÔNG set Content-Type, fetch sẽ tự build Boundary cho FormData
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Tạo gói tour thành công!');
                    window.location.href = 'manage-packages.php';
                } else {
                    alert(result.message || 'Lỗi khi tạo gói tour');
                }
            } catch (err) {
                console.error(err);
                alert("Lỗi kết nối API.");
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // 2. LIST PACKAGES (Danh sách Tour)
    const packageTableBody = document.querySelector('.table tbody');
    if (packageTableBody && document.getElementById('search-form-admin')) {
        loadPackages();
        
        // Handle Search Form
        const searchForm = document.getElementById('search-form-admin');
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const search = document.getElementById('search').value;
            const type = document.getElementById('type').value;
            let location = document.getElementById('location').value;
            
            // Slugify location per architecture rules
            if (location) {
                location = location.toLowerCase()
                    .normalize('NFD') // Tách dấu
                    .replace(/[\u0300-\u036f]/g, '') // Bỏ dấu
                    .replace(/đ/g, 'd').replace(/Đ/g, 'D') // Đổi đ/Đ thành d/D
                    .replace(/[^a-z0-9 ]/g, '') // Xóa ký tự đặc biệt (giữ lại khoảng trắng)
                    .trim()
                    .replace(/\s+/g, '-'); // Thay khoảng trắng bằng gạch ngang
            }
            
            loadPackages(search, type, location);
        });
    }

    // 3. Xóa Tour
    window.deletePackage = async function(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa gói tour này không?')) return;
        
        const token = localStorage.getItem('jwt_token');
        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + `admin/tours/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (result.success) {
                alert('Xóa thành công');
                loadPackages(); // Reload table
            } else {
                alert(result.message || "Lỗi xóa tour");
            }
        } catch(err) {
            console.error(err);
            alert("Lỗi kết nối");
        }
    };
});

// Hàm Load danh sách
async function loadPackages(search = '', type = '', location = '') {
    const tbody = document.querySelector('.table tbody');
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">Đang tải dữ liệu...</td></tr>';
    
    const token = localStorage.getItem('jwt_token');
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (type) params.append('type', type);
    if (location) params.append('location', location);
    
    try {
        const response = await fetch((window.BASE_API_URL || '/tour1/api/') + `admin/tours?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.status === 401) {
            alert("Bạn không có quyền hoặc phiên đăng nhập hết hạn.");
            window.location.href = '../admin/index.php';
            return;
        }

        const result = await response.json();
        
        if (result.success) {
            const tours = result.data;
            if (tours.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">Chưa có gói tour nào.</div></td></tr>`;
            } else {
                tbody.innerHTML = tours.map(tour => {
                    const price = new Intl.NumberFormat('vi-VN').format(tour.PackagePrice) + ' đ';
                    const date = new Date(tour.Creationdate).toLocaleDateString('vi-VN');
                    return `
                        <tr>
                            <td>PKG-${tour.PackageId}</td>
                            <td>${tour.PackageName}</td>
                            <td>${tour.PackageType}</td>
                            <td>${tour.PackageLocation}</td>
                            <td>${tour.TourDuration}</td>
                            <td>${price}</td>
                            <td>${date}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-danger" onclick="deletePackage(${tour.PackageId})">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        } else {
            tbody.innerHTML = `<tr><td colspan="8" style="color:red;text-align:center;">Lỗi: ${result.message}</td></tr>`;
        }
    } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="8" style="color:red;text-align:center;">Lỗi kết nối tải dữ liệu.</td></tr>';
    }
}
