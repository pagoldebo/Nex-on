/**
 * Nex-on Admin Theme Initializer
 * Instant theme detection to prevent Flash of Unstyled Content (FOUC).
 */
(function() {
    try {
        var savedTheme = localStorage.getItem('admin_theme');
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else if (savedTheme === 'light') {
            document.documentElement.classList.remove('dark');
        } else if (document.cookie.indexOf('admin_theme=dark') !== -1) {
            document.documentElement.classList.add('dark');
        }
    } catch (e) {}
})();
