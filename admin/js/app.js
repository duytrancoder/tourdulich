const ready = (cb) => {
  if (document.readyState !== "loading") {
    cb();
  } else {
    document.addEventListener("DOMContentLoaded", cb);
  }
};

ready(() => {
  const sidebar = document.getElementById('adminSidebar');
  const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
  const profileToggle = document.querySelector('[data-profile-toggle]');
  const profileMenu = document.getElementById('adminProfileMenu');

  // Create overlay for mobile sidebar
  let overlay = document.querySelector('.sidebar-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
  }

  const closeSidebar = () => {
    if (sidebar) {
      sidebar.classList.remove('is-visible');
      overlay.classList.remove('is-visible');
      if (sidebarToggle) {
        sidebarToggle.setAttribute('aria-expanded', 'false');
      }
    }
  };

  if (sidebar && sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
      const isVisible = sidebar.classList.toggle('is-visible');
      overlay.classList.toggle('is-visible', isVisible);
      sidebarToggle.setAttribute('aria-expanded', isVisible ? 'true' : 'false');
    });
  }

  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Close sidebar on window resize if desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 960) {
      closeSidebar();
    }
  });

  if (profileToggle && profileMenu) {
    profileToggle.addEventListener('click', (event) => {
      event.preventDefault();
      const isOpen = profileMenu.classList.toggle('is-open');
      profileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (event) => {
      if (!profileMenu.contains(event.target) && !profileToggle.contains(event.target)) {
        profileMenu.classList.remove('is-open');
        profileToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
});
