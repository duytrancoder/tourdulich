// Simple Itinerary Carousel Navigation
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.itinerary-cards');
    const wrapper = document.querySelector('.itinerary-wrapper');

    if (!carousel || !wrapper) return;

    // Create navigation buttons
    const nav = document.createElement('div');
    nav.className = 'carousel-nav';

    // Previous button
    const prevBtn = document.createElement('button');
    prevBtn.className = 'carousel-btn carousel-prev';
    prevBtn.setAttribute('aria-label', 'Lộ trình trước');
    prevBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
        </svg>
    `;

    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.className = 'carousel-btn carousel-next';
    nextBtn.setAttribute('aria-label', 'Lộ trình tiếp theo');
    nextBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
        </svg>
    `;

    nav.appendChild(prevBtn);
    nav.appendChild(nextBtn);
    wrapper.appendChild(nav);

    // Scroll amount
    function getScrollAmount() {
        const card = carousel.querySelector('.itinerary-card');
        return card ? card.offsetWidth + 20 : 380;
    }

    // Navigation handlers
    prevBtn.addEventListener('click', function () {
        carousel.scrollBy({
            left: -getScrollAmount(),
            behavior: 'smooth'
        });
    });

    nextBtn.addEventListener('click', function () {
        carousel.scrollBy({
            left: getScrollAmount(),
            behavior: 'smooth'
        });
    });

    // Update button states
    function updateButtons() {
        const atStart = carousel.scrollLeft <= 1;
        const atEnd = carousel.scrollLeft >= carousel.scrollWidth - carousel.clientWidth - 1;

        prevBtn.disabled = atStart;
        nextBtn.disabled = atEnd;
    }

    carousel.addEventListener('scroll', updateButtons);
    window.addEventListener('resize', updateButtons);
    updateButtons();
});
