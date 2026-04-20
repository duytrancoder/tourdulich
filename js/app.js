const ready = (callback) => {
  if (document.readyState !== "loading") {
    callback();
  } else {
    document.addEventListener("DOMContentLoaded", callback);
  }
};

ready(() => {
  const navBar = document.querySelector('.nav-bar');
  const navToggle = document.querySelector('.nav-toggle');
  const nav = document.getElementById('siteNav');
  const search = document.querySelector('.nav-search');
  let lastScrollY = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;
  let ticking = false;

  const updateNavOnScroll = () => {
    if (!navBar) return;

    const currentY = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;
    const navMenuOpen = nav && nav.classList.contains('is-open');

    if (currentY <= 10 || navMenuOpen) {
      navBar.classList.remove('nav-hidden');
      lastScrollY = currentY;
      return;
    }

    // Scroll down -> hide nav; scroll up -> show nav (anywhere on the page).
    // Use small thresholds to prevent flicker on tiny scroll noise.
    const goingDown = currentY > lastScrollY + 8;
    const goingUp = currentY < lastScrollY - 2;
    if (goingDown) navBar.classList.add('nav-hidden');
    if (goingUp) navBar.classList.remove('nav-hidden');

    lastScrollY = currentY;
  };

  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', isOpen);
      if (navBar) {
        navBar.classList.remove('nav-hidden');
      }
      if (search) {
        search.classList.toggle('is-open');
      }
    });
  }

  window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(() => {
      updateNavOnScroll();
      ticking = false;
    });
  }, { passive: true });

  const openModal = (id) => {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.add('is-visible');
      document.body.style.overflow = 'hidden';
    }
  };

  const closeModal = (modal) => {
    if (modal) {
      modal.classList.remove('is-visible');
      document.body.style.overflow = '';
    }
  };

  document.querySelectorAll('[data-modal-target]').forEach((trigger) => {
    trigger.addEventListener('click', (event) => {
      const targetId = trigger.getAttribute('data-modal-target');
      if (targetId) {
        event.preventDefault();
        openModal(targetId);
      }
    });
  });

  document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      const modal = button.closest('.modal');
      closeModal(modal);
    });
  });

  document.querySelectorAll('.modal').forEach((modal) => {
    modal.addEventListener('click', (event) => {
      if (event.target === modal) {
        closeModal(modal);
      }
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      document.querySelectorAll('.modal.is-visible').forEach(closeModal);
    }
  });
});
