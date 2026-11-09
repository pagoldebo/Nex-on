/**
 * Client-Side WebP Image Converter & Optimizer for Nex-on
 * 
 * Automatically converts all uploaded images (JPEG, JPG, PNG, etc.) to WebP format
 * directly on the uploader's device (client machine) before transmitting to the server.
 * This eliminates server CPU spikes and keeps server memory clean and lightning fast.
 * 
 * Client-Side WebP converter and image optimizer.
 * 
 * Features:
 * - Auto-detects all file inputs containing images
 * - Converts ANY image (JPG, PNG, BMP, TIFF) to WebP client-side via HTML5 Canvas
 * - Targets optimal size range (150-250KB) using binary search quality tuning
 * - Safari/iOS compatible: tests WebP support and falls back gracefully
 * - Blocks form submission during processing to prevent premature upload
 * - Works with single and multiple file inputs
 * - Modern DataTransfer API with FormData fallback
 * - Dispatches 'webp:converted' custom event for reactive frameworks (Alpine.js)
 */
(function () {
    'use strict';

    /* ================= CONFIGURATION ================= */
    const CONFIG = {
        TARGET_MIN: 150 * 1024,     // 150 KB minimum
        TARGET_MAX: 250 * 1024,     // 250 KB maximum
        MAX_WIDTH: 2000,            // Max image width (px)
        MIN_QUALITY: 0.2,           // Minimum quality
        MAX_QUALITY: 0.95,          // Maximum quality
        MAX_ITERATIONS: 15,         // Max optimization attempts (binary search converges fast)
        INITIAL_QUALITY: 0.82,      // Starting quality
        FALLBACK_MAX_WIDTH: 1600,   // Fallback resize if conversion keeps failing
        ABSOLUTE_MAX_SIZE: 2 * 1024 * 1024  // 2MB absolute max
    };

    /* ================= STATE ================= */
    let processingCount = 0;
    let avifSupported = null; // null = not tested, true/false after test
    let webpSupported = null; // null = not tested yet, true/false after test
    const originalButtonStates = new WeakMap();

    /* ================= FORMAT SUPPORT DETECTION ================= */
    function detectAvifSupport() {
        return new Promise(resolve => {
            try {
                const canvas = document.createElement('canvas');
                canvas.width = 1;
                canvas.height = 1;
                canvas.toBlob(function (blob) {
                    avifSupported = (blob && blob.type === 'image/avif' && blob.size > 0);
                    if (avifSupported) {
                        console.log('[Nexon Image Optimizer] ✅ Client-side AVIF encoding supported on this device');
                    }
                    resolve(avifSupported);
                }, 'image/avif', 0.5);
            } catch (e) {
                avifSupported = false;
                resolve(false);
            }
        });
    }

    function detectWebPSupport() {
        return new Promise(resolve => {
            try {
                const canvas = document.createElement('canvas');
                canvas.width = 1;
                canvas.height = 1;
                canvas.toBlob(function (blob) {
                    if (blob && blob.type === 'image/webp' && blob.size > 0) {
                        webpSupported = true;
                        console.log('[Nexon Image Optimizer] ✅ Client-side WebP encoding supported on this device');
                    } else {
                        webpSupported = false;
                        console.log('[Nexon Image Optimizer] ⚠️ WebP encoding not supported on this browser, using optimized JPEG');
                    }
                    resolve(webpSupported);
                }, 'image/webp', 0.5);
            } catch (e) {
                webpSupported = false;
                console.log('[Nexon Image Optimizer] ⚠️ WebP encoding test failed, using JPEG fallback');
                resolve(false);
            }
        });
    }

    // Run detections immediately
    detectAvifSupport();
    detectWebPSupport();

    /* ================= DATATRANSFER SUPPORT DETECTION ================= */
    let dataTransferSupported = true;
    try {
        const dt = new DataTransfer();
        dataTransferSupported = typeof dt.items !== 'undefined' && typeof dt.items.add === 'function';
    } catch (e) {
        dataTransferSupported = false;
        console.log('[Nexon WebP Converter] ⚠️ DataTransfer API not supported, using FormData fallback');
    }

    /* ================= FULL-PAGE LOADER OVERLAY ================= */
    let optimizingCount = 0;
    let fullPageOverlay = null;

    // Inject loader CSS once
    const loaderStyle = document.createElement('style');
    loaderStyle.textContent = `
        .webp-fullpage-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 999999;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            transition: opacity 0.2s ease;
        }
        .webp-fullpage-box {
            background: white;
            border-radius: 20px;
            padding: 32px 48px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            text-align: center;
            border: 1px solid rgba(226, 232, 240, 0.8);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            max-width: 90vw;
        }
        .webp-fullpage-spinner {
            width: 44px; height: 44px;
            border: 4px solid #f1f5f9;
            border-top: 4px solid #e11d48;
            border-radius: 50%;
            animation: webpSpin 0.7s linear infinite;
        }
        .webp-fullpage-text {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .webp-fullpage-sub {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }
        @keyframes webpSpin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(loaderStyle);

    function showFullPageLoader() {
        optimizingCount++;
        if (fullPageOverlay) return;

        fullPageOverlay = document.createElement('div');
        fullPageOverlay.className = 'webp-fullpage-overlay';
        fullPageOverlay.innerHTML = `
            <div class="webp-fullpage-box">
                <div class="webp-fullpage-spinner"></div>
                <div class="webp-fullpage-text">Converting Image to WebP…</div>
                <div class="webp-fullpage-sub">Processing directly on your machine for lightning-fast upload</div>
            </div>
        `;
        document.body.appendChild(fullPageOverlay);
    }

    function hideFullPageLoader() {
        optimizingCount--;
        if (optimizingCount <= 0) {
            optimizingCount = 0;
            if (fullPageOverlay && fullPageOverlay.parentNode) {
                fullPageOverlay.parentNode.removeChild(fullPageOverlay);
            }
            fullPageOverlay = null;
        }
    }

    /* ================= UI HELPERS ================= */
    function lockFormSubmission(formElement) {
        if (!formElement) return;

        const submitBtns = formElement.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitBtns.forEach(btn => {
            if (!originalButtonStates.has(btn)) {
                originalButtonStates.set(btn, {
                    disabled: btn.disabled,
                    html: btn.innerHTML
                });
            }
            btn.disabled = true;
            if (btn.tagName === 'BUTTON') {
                btn.innerHTML = `<span class="inline-flex items-center gap-2"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Optimizing to WebP...</span>`;
            }
        });
    }

    function unlockFormSubmission(formElement) {
        if (!formElement) return;

        const submitBtns = formElement.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitBtns.forEach(btn => {
            const original = originalButtonStates.get(btn);
            if (original) {
                btn.disabled = original.disabled;
                if (btn.tagName === 'BUTTON') {
                    btn.innerHTML = original.html;
                }
                originalButtonStates.delete(btn);
            } else {
                btn.disabled = false;
            }
        });
    }

    /* ================= CORE OPTIMIZER ================= */
    async function tryEncode(canvas, format, quality) {
        return new Promise(resolve => {
            try {
                canvas.toBlob(blob => resolve(blob), format, quality);
            } catch (e) {
                resolve(null);
            }
        });
    }

    async function findOptimalQuality(canvas, format) {
        let low = CONFIG.MIN_QUALITY;
        let high = CONFIG.MAX_QUALITY;
        let bestBlob = null;

        const testBlob = await tryEncode(canvas, format, 0.5);
        if (!testBlob) {
            return null;
        }

        const highQBlob = await tryEncode(canvas, format, CONFIG.MAX_QUALITY);
        if (highQBlob && highQBlob.size <= CONFIG.TARGET_MAX) {
            return { blob: highQBlob, quality: CONFIG.MAX_QUALITY };
        }

        if (highQBlob && highQBlob.size < CONFIG.TARGET_MIN) {
            return { blob: highQBlob, quality: CONFIG.MAX_QUALITY };
        }

        for (let i = 0; i < CONFIG.MAX_ITERATIONS; i++) {
            const mid = (low + high) / 2;
            const blob = await tryEncode(canvas, format, mid);

            if (!blob) {
                return bestBlob ? { blob: bestBlob, quality: mid } : null;
            }

            bestBlob = blob;

            if (blob.size >= CONFIG.TARGET_MIN && blob.size <= CONFIG.TARGET_MAX) {
                return { blob, quality: mid };
            }

            if (blob.size > CONFIG.TARGET_MAX) {
                high = mid;
            } else {
                low = mid;
            }

            if (high - low < 0.01) {
                return { blob: bestBlob, quality: mid };
            }
        }

        return bestBlob ? { blob: bestBlob, quality: (low + high) / 2 } : null;
    }

    async function optimizeImage(file) {
        // Skip non-image files (e.g. videos, documents, PDFs)
        if (!file.type.startsWith('image/') && !/\.(jpe?g|png|webp|gif|bmp|tiff)$/i.test(file.name)) {
            return file;
        }

        // Skip SVG files to preserve vector graphics crispness and scalability
        if (file.type === 'image/svg+xml' || /\.svg$/i.test(file.name)) {
            return file;
        }

        // If already AVIF or WebP and within max size, return as-is
        if ((file.type === 'image/avif' || file.type === 'image/webp') && file.size <= CONFIG.TARGET_MAX) {
            console.log(`[Nexon Image Optimizer] ${file.name} is already optimal (${(file.size / 1024).toFixed(1)} KB)`);
            return file;
        }

        showFullPageLoader();

        if (avifSupported === null) {
            await detectAvifSupport();
        }
        if (webpSupported === null) {
            await detectWebPSupport();
        }

        return new Promise((resolve) => {
            const img = new Image();
            const reader = new FileReader();

            reader.onload = () => {
                img.onload = async () => {
                    try {
                        let width = img.naturalWidth || img.width;
                        let height = img.naturalHeight || img.height;

                        if (width > CONFIG.MAX_WIDTH) {
                            height = Math.round(height * (CONFIG.MAX_WIDTH / width));
                            width = CONFIG.MAX_WIDTH;
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        const formats = [];
                        if (avifSupported) {
                            formats.push({ mime: 'image/avif', ext: 'avif' });
                        }
                        if (webpSupported) {
                            formats.push({ mime: 'image/webp', ext: 'webp' });
                        }
                        formats.push({ mime: 'image/jpeg', ext: 'jpg' });

                        let finalBlob = null;
                        let finalExt = 'jpg';

                        for (const format of formats) {
                            const result = await findOptimalQuality(canvas, format.mime);
                            if (result && result.blob) {
                                finalBlob = result.blob;
                                finalExt = format.ext;
                                break;
                            }
                        }

                        if (!finalBlob) {
                            let fallbackWidth = Math.min(width, CONFIG.FALLBACK_MAX_WIDTH);
                            let fallbackHeight = Math.round(height * (fallbackWidth / width));

                            const fallbackCanvas = document.createElement('canvas');
                            fallbackCanvas.width = fallbackWidth;
                            fallbackCanvas.height = fallbackHeight;
                            const fallbackCtx = fallbackCanvas.getContext('2d');
                            fallbackCtx.drawImage(img, 0, 0, fallbackWidth, fallbackHeight);

                            finalBlob = await tryEncode(fallbackCanvas, 'image/jpeg', 0.75);
                            finalExt = 'jpg';
                        }

                        if (!finalBlob) {
                            console.warn('[Nexon WebP Converter] Fallback to original file for:', file.name);
                            resolve(file);
                            return;
                        }

                        // Create new file with .webp extension
                        const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
                        const newFileName = baseName + '.' + finalExt;
                        const optimizedFile = new File([finalBlob], newFileName, {
                            type: finalBlob.type,
                            lastModified: Date.now()
                        });

                        const originalKB = (file.size / 1024).toFixed(1);
                        const optimizedKB = (optimizedFile.size / 1024).toFixed(1);
                        console.log(`[Nexon WebP Converter] Converted on uploader machine: ${file.name} (${originalKB} KB) ➔ ${newFileName} (${optimizedKB} KB)`);
                        resolve(optimizedFile);

                    } catch (error) {
                        console.error('[Nexon WebP Converter] Conversion error for', file.name, error);
                        resolve(file);
                    } finally {
                        hideFullPageLoader();
                    }
                };

                img.onerror = () => {
                    console.warn('[Nexon WebP Converter] Failed to load image:', file.name);
                    hideFullPageLoader();
                    resolve(file);
                };

                img.src = reader.result;
            };

            reader.onerror = () => {
                console.warn('[Nexon WebP Converter] Failed to read file:', file.name);
                hideFullPageLoader();
                resolve(file);
            };

            reader.readAsDataURL(file);
        });
    }

    // Expose optimizer globally
    window.optimizeImageToWebP = optimizeImage;

    /* ================= FILE INPUT HANDLER ================= */
    async function handleFileInput(input) {
        const files = Array.from(input.files || []);
        if (!files.length) return;

        const hasImages = files.some(f => f.type.startsWith('image/') || /\.(jpe?g|png|webp|gif|bmp|tiff)$/i.test(f.name));
        if (!hasImages) return;

        const form = input.closest('form');

        processingCount++;
        lockFormSubmission(form);

        try {
            const optimizedFiles = [];

            for (const file of files) {
                if (file.type.startsWith('image/') || /\.(jpe?g|png|webp|gif|bmp|tiff)$/i.test(file.name)) {
                    const optimized = await optimizeImage(file);
                    optimizedFiles.push(optimized);
                } else {
                    optimizedFiles.push(file);
                }
            }

            if (dataTransferSupported) {
                try {
                    const dt = new DataTransfer();
                    for (const f of optimizedFiles) {
                        dt.items.add(f);
                    }
                    input.files = dt.files;
                } catch (e) {
                    console.warn('[Nexon WebP Converter] DataTransfer assignment failed, using FormData fallback');
                    input._optimizedFiles = optimizedFiles;
                    dataTransferSupported = false;
                }
            } else {
                input._optimizedFiles = optimizedFiles;
            }

            // Dispatch event for Alpine.js or custom preview listeners to update
            const customEvt = new CustomEvent('webp:converted', {
                bubbles: true,
                detail: { files: optimizedFiles }
            });
            input.dispatchEvent(customEvt);

            // Re-fire a change event marked as already converted so listeners can re-run previews
            const changeEvt = new Event('change', { bubbles: true });
            changeEvt._isWebpConverted = true;
            input.dispatchEvent(changeEvt);

        } catch (error) {
            console.error('[Nexon WebP Converter] Handler error:', error);
        } finally {
            processingCount--;
            if (processingCount === 0) {
                unlockFormSubmission(form);
            }
        }
    }

    /* ================= EVENT LISTENERS ================= */
    document.addEventListener('change', function (e) {
        // Prevent infinite loop from our synthetic change event
        if (e._isWebpConverted) return;

        const input = e.target;
        if (input.tagName !== 'INPUT' || input.type !== 'file') return;
        if (input.name === 'site_logo' || input.id === 'siteLogoInput' || input.hasAttribute('data-no-webp') || input.hasAttribute('data-manual-optimize')) {
            console.log('[Nexon WebP Converter] Skipping site logo input to preserve PNG vector/format');
            return;
        }

        const accept = (input.getAttribute('accept') || '').toLowerCase();
        const files = Array.from(input.files || []);
        if (!files.length) return;

        const isImage = accept.includes('image') || 
                        accept.includes('.png') || 
                        accept.includes('.jpg') || 
                        accept.includes('.jpeg') || 
                        accept.includes('.webp') ||
                        files.some(f => f.type.startsWith('image/') || /\.(jpe?g|png|webp|gif|bmp|tiff)$/i.test(f.name));

        if (!isImage) return;

        // Skip if all files are already WebP or SVG, and within target size
        const allAlreadyOptimal = files.every(f => 
            (f.type === 'image/svg+xml' || f.name.toLowerCase().endsWith('.svg')) ||
            ((f.type === 'image/webp' || f.name.toLowerCase().endsWith('.webp')) && f.size <= CONFIG.TARGET_MAX)
        );
        if (allAlreadyOptimal) return;

        handleFileInput(input);
    }, true);

    // Block form submission while processing
    document.addEventListener('submit', function (e) {
        if (processingCount > 0) {
            e.preventDefault();
            e.stopPropagation();
            alert('Please wait while images are being converted to WebP on your device...');
            return false;
        }

        if (!dataTransferSupported) {
            const form = e.target;
            const fileInputs = form.querySelectorAll('input[type="file"]');
            let hasOptimized = false;

            fileInputs.forEach(input => {
                if (input._optimizedFiles && input._optimizedFiles.length > 0) {
                    hasOptimized = true;
                }
            });

            if (hasOptimized) {
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData(form);
                fileInputs.forEach(input => {
                    if (input._optimizedFiles && input._optimizedFiles.length > 0) {
                        const name = input.name;
                        formData.delete(name);
                        input._optimizedFiles.forEach(file => {
                            formData.append(name, file);
                        });
                    }
                });

                fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData
                }).then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Upload failed. Please try again.');
                    }
                }).catch(err => {
                    console.error('[Nexon WebP Converter] FormData submit error:', err);
                    alert('Upload failed. Please try again.');
                });
            }
        }
    }, true);

    console.log('[Nexon WebP Converter] Initialized - Auto-converting all image uploads to WebP on uploader machine');
})();
