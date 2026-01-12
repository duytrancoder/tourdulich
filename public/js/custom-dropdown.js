// Custom Dropdown Menu JavaScript
class CustomSelect {
    constructor(selectElement) {
        this.selectElement = selectElement;
        this.selectedValue = selectElement.value;
        this.selectedText = selectElement.options[selectElement.selectedIndex]?.text || 'Chọn...';

        this.createCustomSelect();
        this.attachEvents();
    }

    createCustomSelect() {
        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-wrapper';

        // Create button
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'custom-select-button';
        button.textContent = this.selectedText;
        button.setAttribute('aria-haspopup', 'listbox');
        button.setAttribute('aria-expanded', 'false');

        // Create dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'custom-select-dropdown';
        dropdown.setAttribute('role', 'listbox');

        // Create options
        Array.from(this.selectElement.options).forEach((option, index) => {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'custom-select-option';
            optionDiv.textContent = option.text;
            optionDiv.dataset.value = option.value;
            optionDiv.setAttribute('role', 'option');
            optionDiv.setAttribute('tabindex', '0');

            if (option.value === this.selectedValue) {
                optionDiv.classList.add('selected');
            }

            dropdown.appendChild(optionDiv);
        });

        // Insert custom select
        this.selectElement.parentNode.insertBefore(wrapper, this.selectElement);
        wrapper.appendChild(this.selectElement);
        wrapper.appendChild(button);
        wrapper.appendChild(dropdown);

        this.wrapper = wrapper;
        this.button = button;
        this.dropdown = dropdown;
    }

    attachEvents() {
        // Toggle dropdown
        this.button.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Select option
        this.dropdown.querySelectorAll('.custom-select-option').forEach(option => {
            option.addEventListener('click', (e) => {
                this.selectOption(option);
            });

            // Keyboard support
            option.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.selectOption(option);
                }
            });
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.close();
            }
        });

        // Keyboard navigation
        this.button.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.open();
                this.dropdown.querySelector('.custom-select-option')?.focus();
            } else if (e.key === 'Escape') {
                this.close();
            }
        });
    }

    toggle() {
        if (this.dropdown.classList.contains('active')) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        // Close all other dropdowns
        document.querySelectorAll('.custom-select-dropdown.active').forEach(dd => {
            if (dd !== this.dropdown) {
                dd.classList.remove('active');
                dd.previousElementSibling.classList.remove('active');
                dd.previousElementSibling.setAttribute('aria-expanded', 'false');
            }
        });

        this.dropdown.classList.add('active');
        this.button.classList.add('active');
        this.button.setAttribute('aria-expanded', 'true');
    }

    close() {
        this.dropdown.classList.remove('active');
        this.button.classList.remove('active');
        this.button.setAttribute('aria-expanded', 'false');
    }

    selectOption(optionElement) {
        const value = optionElement.dataset.value;
        const text = optionElement.textContent;

        // Update native select
        this.selectElement.value = value;
        this.selectElement.dispatchEvent(new Event('change', { bubbles: true }));

        // Update button text
        this.button.textContent = text;

        // Update selected class
        this.dropdown.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        optionElement.classList.add('selected');

        // Close dropdown
        this.close();
        this.button.focus();
    }
}

// Initialize all select elements with custom dropdown
document.addEventListener('DOMContentLoaded', function () {
    // Only apply to selects in hero card form
    const heroSelects = document.querySelectorAll('.hero__card select');
    heroSelects.forEach(select => {
        new CustomSelect(select);
    });
});
