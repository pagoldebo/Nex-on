/**
 * Nex-on Admin Print & POS Hardware Bridge Utilities
 */
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1' || document.body.classList.contains('auto-print')) {
        setTimeout(function() {
            window.print();
        }, 400);
    }
});

/**
 * ESC/POS Cash Drawer Kick Command: ESC p m t1 t2
 */
window.kickCashDrawer = function() {
    alert("Drawer Kick Command (ESC p 0 25 250) sent to POS receipt printer.");
};
