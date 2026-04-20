(function () {
  var modal = document.getElementById("reviewsModal");
  var openBtn = document.getElementById("openReviewsModal");
  if (!modal || !openBtn) {
    return;
  }

  var closeBtn = document.getElementById("closeReviewsModal");
  var listContainer = document.getElementById("reviewListContainer");
  var emptyState = document.getElementById("reviewEmptyState");
  var filterBar = document.getElementById("reviewFilterBar");
  var reviews = Array.isArray(window.PACKAGE_REVIEWS) ? window.PACKAGE_REVIEWS : [];

  function escapeHtml(text) {
    return String(text || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function formatDate(rawValue) {
    if (!rawValue) {
      return "";
    }
    var date = new Date(rawValue);
    if (Number.isNaN(date.getTime())) {
      return escapeHtml(rawValue);
    }
    return date.toLocaleDateString("vi-VN");
  }

  function renderStars(rating) {
    var html = "";
    for (var i = 1; i <= 5; i++) {
      var className = i <= rating ? "is-active" : "";
      html += '<span class="' + className + '">&#9733;</span>';
    }
    return html;
  }

  function renderList(filterValue) {
    var wrapper = document.querySelector(".review-list-wrapper");
    if (wrapper) {
      wrapper.classList.remove("is-ready");
      wrapper.classList.add("is-updating");
    }

    var filtered = reviews.filter(function (review) {
      if (filterValue === "all") {
        return true;
      }
      return parseInt(review.rating, 10) === parseInt(filterValue, 10);
    });

    if (!filtered.length) {
      listContainer.innerHTML = "";
      emptyState.style.display = "block";
      if (wrapper) {
        setTimeout(function () {
          wrapper.classList.remove("is-updating");
          wrapper.classList.add("is-ready");
        }, 80);
      }
      return;
    }

    emptyState.style.display = "none";
    listContainer.innerHTML = filtered
      .map(function (review) {
        return (
          '<div class="review-item">' +
          '<div class="review-item-head">' +
          '<span class="review-avatar">' + escapeHtml(review.initial || "K") + "</span>" +
          '<div class="review-meta">' +
          '<span class="review-author">' + escapeHtml(review.name || "Khách hàng") + "</span>" +
          '<span class="review-date">' + formatDate(review.createdAt) + "</span>" +
          "</div>" +
          "</div>" +
          '<div class="review-item-stars">' + renderStars(parseInt(review.rating, 10) || 0) + "</div>" +
          '<p class="review-content">' + escapeHtml(review.comment || "") + "</p>" +
          "</div>"
        );
      })
      .join("");

    if (wrapper) {
      setTimeout(function () {
        wrapper.classList.remove("is-updating");
        wrapper.classList.add("is-ready");
      }, 80);
    }
  }

  function setActiveFilter(button) {
    filterBar.querySelectorAll(".review-filter-chip").forEach(function (chip) {
      chip.classList.remove("is-active");
    });
    button.classList.add("is-active");
  }

  function openModal() {
    modal.classList.add("is-visible");
    modal.setAttribute("aria-hidden", "false");
    renderList("all");
  }

  function closeModal() {
    modal.classList.remove("is-visible");
    modal.setAttribute("aria-hidden", "true");
  }

  openBtn.addEventListener("click", openModal);
  if (closeBtn) {
    closeBtn.addEventListener("click", closeModal);
  }

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  filterBar.querySelectorAll(".review-filter-chip").forEach(function (chip) {
    chip.addEventListener("click", function () {
      setActiveFilter(chip);
      renderList(chip.getAttribute("data-rating") || "all");
    });
  });
})();
