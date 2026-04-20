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

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    if (!ratingInput || !ratingInput.value) {
      alert("Vui lòng chọn số sao trước khi gửi đánh giá.");
      return;
    }

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Đang gửi...";
    }

    var payload = new URLSearchParams(new FormData(form));
    fetch(baseUrl + "review/submit", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: payload.toString(),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        alert(result.message || "Đã xử lý yêu cầu.");
        if (result.status === "success") {
          window.location.reload();
        }
      })
      .catch(function () {
        alert("Không thể gửi đánh giá. Vui lòng thử lại.");
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = "Gửi đánh giá";
        }
      });
  });
})();
