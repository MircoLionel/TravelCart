import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

function applyTheme(mode) {
    const root = document.documentElement;
    root.classList.toggle('dark', mode === 'dark');
    root.dataset.theme = mode;
    root.dispatchEvent(new CustomEvent('theme-changed', { detail: mode }));
}

window.toggleTheme = () => {
    const isDark = document.documentElement.classList.contains('dark');
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem('theme', next);
    applyTheme(next);
};

window.syncTheme = () => {
    const saved = localStorage.getItem('theme');
    const mode = saved ?? (prefersDark.matches ? 'dark' : 'light');
    applyTheme(mode);
};

prefersDark.addEventListener('change', () => {
    if (!localStorage.getItem('theme')) {
        window.syncTheme();
    }
});

window.syncTheme();

Alpine.start();
