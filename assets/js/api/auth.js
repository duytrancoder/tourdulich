/**
 * API Auth Handler for Frontend
 */

const API_BASE_URL = '/tour1/api';

// Utility to handle fetch logic with JSON headers
async function apiFetch(endpoint, method = 'GET', data = null) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    const token = localStorage.getItem('jwt_token');
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const config = {
        method: method,
        headers: headers
    };

    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        config.body = JSON.stringify(data);
    }

    const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
    const result = await response.json();
    
    // Auto logout if unauthorized
    if (response.status === 401) {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_data');
        // Optional: redirect to home
    }

    return { status: response.status, data: result };
}

// Handle Login Form
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('#signin-modal form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // CHẶN reload trang
            
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Đang xử lý...';
            submitBtn.disabled = true;

            const email = loginForm.querySelector('#signin-email').value;
            const password = loginForm.querySelector('#signin-password').value;

            try {
                const response = await apiFetch('/auth/login', 'POST', { email, password });
                
                if (response.data.success) {
                    // Lưu JWT vào localStorage
                    localStorage.setItem('jwt_token', response.data.data.token);
                    localStorage.setItem('user_data', JSON.stringify(response.data.data.user));
                    
                    alert('Đăng nhập thành công!');
                    window.location.reload(); // Reload để cập nhật UI header (hoặc dùng JS DOM update)
                } else {
                    alert(response.data.message || 'Đăng nhập thất bại');
                }
            } catch (err) {
                alert('Có lỗi xảy ra, vui lòng thử lại');
                console.error(err);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Handle Signup Form
    const signupForm = document.querySelector('#signup-modal form');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // CHẶN reload trang
            
            const submitBtn = signupForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Đang xử lý...';
            submitBtn.disabled = true;

            const fname = signupForm.querySelector('#signup-name').value;
            const mobilenumber = signupForm.querySelector('#signup-phone').value;
            const email = signupForm.querySelector('#signup-email').value;
            const password = signupForm.querySelector('#signup-password').value;

            try {
                const response = await apiFetch('/auth/register', 'POST', { fname, mobilenumber, email, password });
                
                if (response.data.success) {
                    alert('Đăng ký thành công! Vui lòng đăng nhập.');
                    // Tự động chuyển sang form đăng nhập
                    document.getElementById('signup-modal').classList.remove('is-visible');
                    document.getElementById('signin-modal').classList.add('is-visible');
                    signupForm.reset();
                } else {
                    let errorMsg = response.data.message;
                    if (response.data.errors) {
                        errorMsg += '\n' + Object.values(response.data.errors).join('\n');
                    }
                    alert(errorMsg);
                }
            } catch (err) {
                alert('Có lỗi xảy ra, vui lòng thử lại');
                console.error(err);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});
