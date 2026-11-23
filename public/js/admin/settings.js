/**
 * ==========================================================================
 * NEXON ADMIN PANEL — SETTINGS & BRANDING PREVIEW CONTROLLER
 * Real-time Favicon, Logo & Footer Logo File Reader Previews
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    const logoInput = document.getElementById('siteLogoInput');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const previewEl = document.getElementById('newLogoPreview');
                    const wrapperEl = document.getElementById('newLogoPreviewWrapper');
                    if (previewEl && wrapperEl) {
                        previewEl.src = evt.target.result;
                        wrapperEl.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const footerLogoInput = document.getElementById('footerLogoInput');
    if (footerLogoInput) {
        footerLogoInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const previewEl = document.getElementById('newFooterLogoPreview');
                    const wrapperEl = document.getElementById('newFooterLogoPreviewWrapper');
                    if (previewEl && wrapperEl) {
                        previewEl.src = evt.target.result;
                        wrapperEl.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
