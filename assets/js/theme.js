/**
 * BELAMITECH — Theme Toggle (Catppuccin Macchiato ↔ Latte)
 * Persists user preference in localStorage
 */
(function () {
    var STORAGE_KEY = 'belamitech_theme';

    function getPreferred() {
        try {
            return localStorage.getItem(STORAGE_KEY) || 'dark';
        } catch (e) {
            return 'dark';
        }
    }

    function applyTheme(theme) {
        if (theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {}
    }

    function toggleTheme() {
        var current = getPreferred();
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    // Apply on load (before paint)
    applyTheme(getPreferred());

    // Expose globally — merge with existing
    if (!window.BelaTech) window.BelaTech = {};
    window.BelaTech.toggleTheme = toggleTheme;
    window.BelaTech.getTheme = getPreferred;
})();
