/* Shared review modal handler for customer pages */
(function () {
  var modal = document.getElementById("reviewModal");
  if (!modal) {
    return;
  }

  var closeBtn = document.getElementById("reviewModalClose");
  var form = document.getElementById("reviewForm");
  var bookingInput = document.getElementById("reviewBookingId");
  var packageInput = document.getElementById("reviewPackageId");
  var ratingInput = document.getElementById("reviewRating");
  var ratingHint = document.getElementById("ratingHint");
  var submitBtn = form ? form.querySelector('button[type="submit"]') : null;
  var baseUrl = window.BASE_URL_FROM_PHP || "/";
  var stars = Array.prototype.slice.call(
    document.querySelectorAll("#ratingStars .rating-star")
  );
  var ratingLabels = {
    1: "1 sao - Chưa hài lòng",
    2: "2 sao - Tạm ổn",
    3: "3 sao - Tốt",
    4: "4 sao - Rất tốt",
    5: "5 sao - Tuyệt vời",
  };

  function closeModal() {
    modal.classList.remove("is-visible");
    modal.setAttribute("aria-hidden", "true");
  }

  function updateStars(value) {
    var selected = parseInt(value || 0, 10);
    stars.forEach(function (star, index) {
      if (index < selected) {
        star.classList.add("is-active");
      } else {
        star.classList.remove("is-active");
      }
    });
    if (ratingHint) {
      ratingHint.textContent = selected
        ? ratingLabels[selected]
        : "Chọn số sao bạn muốn đánh giá.";
    }
  }

  document.querySelectorAll(".js-open-review").forEach(function (btn) {
    btn.addEventListener("click", function () {
      if (bookingInput) {
        bookingInput.value = btn.getAttribute("data-booking-id") || "";
      }
      if (packageInput) {
        packageInput.value = btn.getAttribute("data-package-id") || "";
      }
      if (form) {
        form.reset();
      }
      if (ratingInput) {
        ratingInput.value = "";
      }
      updateStars(0);
      modal.classList.add("is-visible");
      modal.setAttribute("aria-hidden", "false");
    });
  });

  stars.forEach(function (star) {
    star.addEventListener("click", function () {
      var value = star.getAttribute("data-value");
      if (ratingInput) {
        ratingInput.value = value;
      }
      updateStars(value);
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener("click", closeModal);
  }

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  if (!form) {
    return;
  }

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    if (!ratingInput || !ratingInput.value) {
      alert("Vui lòng chọn số sao trước khi gửi đánh giá.");
      return;
    }

    // Kiểm tra JWT token (Quy tắc: Không dùng Session, chỉ dùng localStorage)
    var token = localStorage.getItem('jwt_token');
    if (!token) {
      alert("Vui lòng đăng nhập để gửi đánh giá.");
      return;
    }

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Đang gửi...";
    }

    // Payload JSON theo chuẩn REST API mới
    var payload = {
      booking_id: parseInt(bookingInput ? bookingInput.value : 0),
      package_id: parseInt(packageInput ? packageInput.value : 0),
      rating:     parseInt(ratingInput.value),
      comment:    (form.querySelector('#reviewComment') || {}).value || ''
    };

    try {
      var response = await fetch('/tour1/api/user/review', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + token,
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      var result = await response.json();

      if (result.success) {
        closeModal();
        // Reload reviews list mà KHÔNG tải lại toàn bộ trang
        if (result.data && result.data.reviews) {
          reloadReviewList(result.data.reviews);
        }
        alert(result.message || "Cảm ơn bạn đã đánh giá!");
        // Reload bookings để cập nhật nút "Đánh giá" -> đã đánh giá
        if (typeof fetchAccountData === 'function') {
          fetchAccountData(token);
        }
      } else {
        alert(result.message || "Không thể gửi đánh giá. Vui lòng thử lại.");
      }
    } catch (err) {
      console.error("Review submit error:", err);
      alert("Lỗi kết nối máy chủ. Vui lòng thử lại.");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = "Gửi đánh giá";
      }
    }
  });

  /**
   * Cập nhật danh sách review trên trang mà không reload trang
   * result.data.reviews là mảng review mới nhất từ API
   */
  function reloadReviewList(reviews) {
    var container = document.querySelector('.reviews-list, #reviews-section, [data-reviews-container]');
    if (!container || !reviews) return;

    if (reviews.length === 0) {
      container.innerHTML = '<p>Chưa có đánh giá nào.</p>';
      return;
    }

    var html = reviews.map(function(r) {
      var date = new Date(r.CreatedAt).toLocaleDateString('vi-VN');
      var stars = '★'.repeat(r.Rating) + '☆'.repeat(5 - r.Rating);
      return '<div class="review-item">' +
        '<div class="review-header">' +
          '<strong>' + (r.FullName || 'Ẩn danh') + '</strong>' +
          '<span class="review-stars" style="color:#f59e0b">' + stars + '</span>' +
          '<span class="review-date">' + date + '</span>' +
        '</div>' +
        '<p class="review-comment">' + (r.Comment || '') + '</p>' +
        '</div>';
    }).join('');

    container.innerHTML = html;
  }

})();
