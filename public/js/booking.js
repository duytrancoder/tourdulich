document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking-form');
    if (!bookingForm) return;

    // Check login state to toggle button
    const token = localStorage.getItem('jwt_token');
    const btnBook = document.getElementById('btn-book-tour');
    
    if (!token) {
        btnBook.textContent = 'Đăng nhập để đặt tour';
        btnBook.classList.remove('btn');
        btnBook.classList.add('btn-ghost');
        btnBook.type = 'button';
        btnBook.addEventListener('click', (e) => {
            e.preventDefault();
            const signinModal = document.getElementById('signin-modal');
            if (signinModal) signinModal.classList.add('is-visible');
        });
    }

    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!token) return;

        const packageId = document.getElementById('package-id').value;
        const departureDate = document.getElementById('departuredate').value;
        const numberOfPeople = document.getElementById('numberofpeople').value;
        const comment = document.getElementById('comment').value;
        
        const alertBox = document.getElementById('booking-alert');
        alertBox.style.display = 'none';
        
        btnBook.textContent = 'Đang xử lý...';
        btnBook.disabled = true;

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'user/booking', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    packageId: packageId,
                    departureDate: departureDate,
                    numberOfPeople: numberOfPeople,
                    comment: comment
                })
            });
            
            const res = await response.json();
            
            if (res.success) {
                alertBox.className = 'alert success';
                alertBox.innerHTML = `<strong>Thành công:</strong> ${res.message}`;
                alertBox.style.display = 'block';
                bookingForm.reset();
                
                // Show toast globally
                if (typeof showToast === 'function') {
                    showToast(res.message, 'success');
                } else {
                    alert(res.message);
                }
                
                // Redirect to history after 2s
                setTimeout(() => {
                    window.location.href = (window.BASE_URL_FROM_PHP || '/tour1/') + 'user/account#bookings';
                }, 2000);
            } else {
                alertBox.className = 'alert error';
                alertBox.innerHTML = `<strong>Lỗi:</strong> ${res.message}`;
                alertBox.style.display = 'block';
            }
        } catch (error) {
            console.error('Booking error:', error);
            alertBox.className = 'alert error';
            alertBox.innerHTML = `<strong>Lỗi:</strong> Có lỗi xảy ra, vui lòng thử lại sau.`;
            alertBox.style.display = 'block';
        } finally {
            btnBook.textContent = 'Đặt tour';
            btnBook.disabled = false;
        }
    });
});
