

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode management
Alpine.store('theme', {
    mode: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
    
    toggle() {
        this.mode = this.mode === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', this.mode);
        this.applyTheme();
    },
    
    applyTheme() {
        if (this.mode === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    
    init() {
        this.applyTheme();
    }
});

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', () => {
    Alpine.store('theme').init();
});

Alpine.start();
