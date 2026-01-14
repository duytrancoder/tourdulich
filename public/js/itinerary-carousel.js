// Itinerary Section - Vertical Layout (No carousel needed)
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.itinerary-cards');
    const wrapper = document.querySelector('.itinerary-wrapper');

    if (!carousel || !wrapper) return;

    // For vertical layout, we don't need carousel navigation
    // All cards are displayed in a vertical stack
    // Navigation buttons are hidden by default in CSS

    // Optional: Add smooth scroll to top/bottom if there are many items
    const cards = carousel.querySelectorAll('.itinerary-card');

    // Only show navigation if there are more than 5 items
    if (cards.length > 5) {
        // Create navigation buttons for scrolling
        const nav = document.createElement('div');
        nav.className = 'carousel-nav';

        // Scroll Up button
        const upBtn = document.createElement('button');
        upBtn.className = 'carousel-btn carousel-up';
        upBtn.setAttribute('aria-label', 'Cuộn lên');
        upBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
            </svg>
        `;

        // Scroll Down button
        const downBtn = document.createElement('button');
        downBtn.className = 'carousel-btn carousel-down';
        downBtn.setAttribute('aria-label', 'Cuộn xuống');
        downBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
            </svg>
        `;

        nav.appendChild(upBtn);
        nav.appendChild(downBtn);
        wrapper.appendChild(nav);

        // Scroll amount - one card height plus gap
        function getScrollAmount() {
            const card = carousel.querySelector('.itinerary-card');
            return card ? card.offsetHeight + 20 : 200;
        }

        // Navigation handlers - scroll vertically
        upBtn.addEventListener('click', function () {
            carousel.scrollBy({
                top: -getScrollAmount(),
                behavior: 'smooth'
            });
        });

        downBtn.addEventListener('click', function () {
            carousel.scrollBy({
                top: getScrollAmount(),
                behavior: 'smooth'
            });
        });

        // Update button states
        function updateButtons() {
            const atStart = carousel.scrollTop <= 1;
            const atEnd = carousel.scrollTop >= carousel.scrollHeight - carousel.clientHeight - 1;

            upBtn.disabled = atStart;
            downBtn.disabled = atEnd;
        }

        carousel.addEventListener('scroll', updateButtons);
        window.addEventListener('resize', updateButtons);
        updateButtons();
    }
});
