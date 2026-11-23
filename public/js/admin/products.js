/**
 * ==========================================================================
 * NEXON ADMIN PANEL — PRODUCTS MANAGEMENT & MATRIX BUILDER
 * Variants matrix, Color blocks, Size rows, Image uploader, Gallery drag-drop
 * and Bulk Selection Actions for Product Catalog (Create & Edit Modes)
 * ==========================================================================
 */

let colorIndex = 0;
let newColorCounter = 0;
let newSizeCounters = {};

// ===== Color & Size Matrix Handlers =====
function addColorBlock() {
    const tpl = document.getElementById('colorBlockTemplate');
    if (!tpl) return;

    const colorKey = `new${newColorCounter}`;
    newColorCounter++;
    newSizeCounters[colorKey] = 0;

    let html = tpl.innerHTML;
    html = html.replaceAll('__COLOR_INDEX__', (typeof colorIndex !== 'undefined' ? colorIndex : colorKey));

    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;
    const newColorBlock = wrapper.firstElementChild;
    newColorBlock._galleryFiles = [];
    
    const container = document.getElementById('colorsContainer');
    if (container) {
        container.appendChild(newColorBlock);
    }
    colorIndex++;
    
    const addSizeBtn = newColorBlock.querySelector('button[onclick*="addSizeRow"]');
    if (addSizeBtn) {
        addSizeRow(addSizeBtn, colorKey);
    }
}
window.addColorBlock = addColorBlock;

function addSizeRow(triggerBtnOrBlock, colorKey) {
    if (!triggerBtnOrBlock) return;
    const colorBlock = triggerBtnOrBlock.closest ? triggerBtnOrBlock.closest('.color-block') : triggerBtnOrBlock;
    if (!colorBlock) return;

    const colorIdx = colorBlock.dataset.colorIndex || colorKey || '0';
    const sizeContainer = colorBlock.querySelector('.size-rows-container');
    if (!sizeContainer) return;

    const existingRows = sizeContainer.querySelectorAll('.size-row').length;
    const tpl = document.getElementById('sizeRowTemplate');
    if (!tpl) return;

    const sizeKey = `new${(newSizeCounters[colorKey] !== undefined ? newSizeCounters[colorKey]++ : existingRows)}`;
    const html = tpl.innerHTML
        .replaceAll('__COLOR_INDEX__', colorIdx)
        .replaceAll('__SIZE_INDEX__', (colorKey ? sizeKey : existingRows));

    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;
    sizeContainer.appendChild(wrapper.firstElementChild);
}
window.addSizeRow = addSizeRow;

function removeColorBlock(btn) {
    const block = btn.closest('.color-block');
    if (block) block.remove();
}
window.removeColorBlock = removeColorBlock;

// ===== Cover Image Handling =====
function handleCoverImageSelect(input) {
    const block = input.closest('.color-block');
    if (!block) return;
    const img = block.querySelector('.cover-preview-img');
    const icon = block.querySelector('.cover-placeholder-icon');
    const badge = block.querySelector('.cover-badge');
    const clearBtn = block.querySelector('.cover-clear-btn');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        img.src = URL.createObjectURL(file);
        img.classList.remove('hidden');
        if (icon) icon.classList.add('hidden');
        if (badge) badge.classList.remove('hidden');
        if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
        clearCoverImage(input);
    }
}
window.handleCoverImageSelect = handleCoverImageSelect;

function clearCoverImage(btnOrInput) {
    const block = btnOrInput.closest('.color-block');
    if (!block) return;
    const input = block.querySelector('.cover-file-input');
    const img = block.querySelector('.cover-preview-img');
    const icon = block.querySelector('.cover-placeholder-icon');
    const badge = block.querySelector('.cover-badge');
    const clearBtn = block.querySelector('.cover-clear-btn');

    if (input) input.value = '';
    if (img && img.dataset.origSrc) {
        img.src = img.dataset.origSrc;
        img.classList.remove('hidden');
        if (badge) badge.classList.remove('hidden');
        if (icon) icon.classList.add('hidden');
    } else if (img) {
        img.src = '';
        img.classList.add('hidden');
        if (icon) icon.classList.remove('hidden');
        if (badge) badge.classList.add('hidden');
    }
    if (clearBtn) clearBtn.classList.add('hidden');
}
window.clearCoverImage = clearCoverImage;

function toggleRemoveCoverStyle(checkbox) {
    const block = checkbox.closest('.color-block');
    if (!block) return;
    const wrapper = block.querySelector('.cover-preview-wrapper');
    if (wrapper) {
        wrapper.classList.toggle('ring-4', checkbox.checked);
        wrapper.classList.toggle('ring-rose-500', checkbox.checked);
        wrapper.classList.toggle('opacity-30', checkbox.checked);
    }
}
window.toggleRemoveCoverStyle = toggleRemoveCoverStyle;

// ===== Existing Images Reordering & Removal (Edit Mode) =====
function toggleImageRemoveStyle(checkbox) {
    const card = checkbox.closest('.existing-image-card');
    if (!card) return;
    const label = card.querySelector('.remove-label-text');
    if (checkbox.checked) {
        card.classList.add('ring-4', 'ring-rose-500', 'opacity-40');
        if (label) label.textContent = 'Removed';
    } else {
        card.classList.remove('ring-4', 'ring-rose-500', 'opacity-40');
        if (label) label.textContent = 'Remove';
    }
}
window.toggleImageRemoveStyle = toggleImageRemoveStyle;

function moveExistingImageCard(btn, direction) {
    const card = btn.closest('.existing-image-card');
    if (!card) return;
    const grid = card.closest('.existing-images-grid');
    if (!grid) return;
    if (direction === -1 && card.previousElementSibling) {
        grid.insertBefore(card, card.previousElementSibling);
    } else if (direction === 1 && card.nextElementSibling) {
        grid.insertBefore(card.nextElementSibling, card);
    }
    updateExistingGridOrder(grid);
}
window.moveExistingImageCard = moveExistingImageCard;

function updateExistingGridOrder(grid) {
    if (!grid) return;
    const cards = Array.from(grid.querySelectorAll('.existing-image-card'));
    cards.forEach((card, idx) => {
        const badge = card.querySelector('.order-badge');
        if (badge) badge.textContent = `#${idx + 1}`;

        const leftBtn = card.querySelector('.btn-move-left');
        const rightBtn = card.querySelector('.btn-move-right');
        if (leftBtn) {
            leftBtn.classList.toggle('opacity-30', idx === 0);
            leftBtn.classList.toggle('pointer-events-none', idx === 0);
        }
        if (rightBtn) {
            rightBtn.classList.toggle('opacity-30', idx === cards.length - 1);
            rightBtn.classList.toggle('pointer-events-none', idx === cards.length - 1);
        }
    });
}
window.updateExistingGridOrder = updateExistingGridOrder;

function setupExistingImageDragAndDrop(grid) {
    if (!grid) return;
    grid.addEventListener('dragstart', (e) => {
        const card = e.target.closest('.existing-image-card');
        if (!card) return;
        card.classList.add('opacity-40');
        e.dataTransfer.setData('text/plain', card.dataset.imageId);
        window._draggedExistingCard = card;
    });

    grid.addEventListener('dragend', (e) => {
        const card = e.target.closest('.existing-image-card');
        if (card) card.classList.remove('opacity-40');
        grid.querySelectorAll('.existing-image-card').forEach(c => c.classList.remove('ring-2', 'ring-indigo-500'));
        window._draggedExistingCard = null;
    });

    grid.addEventListener('dragover', (e) => {
        e.preventDefault();
        const targetCard = e.target.closest('.existing-image-card');
        if (targetCard && targetCard !== window._draggedExistingCard) {
            targetCard.classList.add('ring-2', 'ring-indigo-500');
        }
    });

    grid.addEventListener('dragleave', (e) => {
        const targetCard = e.target.closest('.existing-image-card');
        if (targetCard) targetCard.classList.remove('ring-2', 'ring-indigo-500');
    });

    grid.addEventListener('drop', (e) => {
        e.preventDefault();
        const targetCard = e.target.closest('.existing-image-card');
        grid.querySelectorAll('.existing-image-card').forEach(c => c.classList.remove('ring-2', 'ring-indigo-500'));

        if (targetCard && window._draggedExistingCard && targetCard !== window._draggedExistingCard) {
            const cards = Array.from(grid.querySelectorAll('.existing-image-card'));
            const draggedIdx = cards.indexOf(window._draggedExistingCard);
            const targetIdx = cards.indexOf(targetCard);
            if (draggedIdx < targetIdx) {
                grid.insertBefore(window._draggedExistingCard, targetCard.nextSibling);
            } else {
                grid.insertBefore(window._draggedExistingCard, targetCard);
            }
            updateExistingGridOrder(grid);
        }
    });
}
window.setupExistingImageDragAndDrop = setupExistingImageDragAndDrop;

// ===== Gallery Images Handling & Drag-Drop =====
function handleGalleryImagesSelect(input) {
    const block = input.closest('.color-block');
    if (!block) return;

    const maxAllowed = parseInt(input.dataset.maxAllowed || '8', 10);
    const incomingFiles = Array.from(input.files || []);
    if (!incomingFiles.length) {
        block._galleryFiles = [];
        renderGalleryPreviews(block, maxAllowed);
        const badge = block.querySelector('.gallery-count-badge');
        if (badge) badge.textContent = `0 / ${maxAllowed} images`;
        return;
    }

    block._galleryFiles = incomingFiles.slice(0, maxAllowed);
    renderGalleryPreviews(block, maxAllowed);

    const badge = block.querySelector('.gallery-count-badge');
    if (badge) {
        badge.textContent = `${block._galleryFiles.length} / ${maxAllowed} images`;
    }
}
window.handleGalleryImagesSelect = handleGalleryImagesSelect;

function syncGalleryFilesToInput(block, maxAllowed) {
    const input = block.querySelector('.gallery-file-input');
    if (!input) return;
    try {
        const dt = new DataTransfer();
        (block._galleryFiles || []).forEach(file => dt.items.add(file));
        input.files = dt.files;
    } catch (e) {}

    const badge = block.querySelector('.gallery-count-badge');
    if (badge) {
        const max = maxAllowed !== undefined ? maxAllowed : parseInt(input.dataset.maxAllowed || '8', 10);
        badge.textContent = `${(block._galleryFiles || []).length} / ${max} images`;
    }
}

function renderGalleryPreviews(block, maxAllowed) {
    const grid = block.querySelector('.gallery-preview-grid');
    if (!grid) return;
    grid.innerHTML = '';
    const files = block._galleryFiles || [];

    if (files.length === 0) {
        grid.classList.add('empty:hidden');
        return;
    }
    grid.classList.remove('empty:hidden');

    files.forEach((file, index) => {
        const card = document.createElement('div');
        card.className = 'gallery-card relative group rounded-xl overflow-hidden border border-slate-200 bg-white shadow-xs select-none aspect-3/4 flex flex-col justify-between cursor-grab active:cursor-grabbing transition transform hover:scale-[1.02]';
        card.draggable = true;
        card.dataset.index = index;

        const url = URL.createObjectURL(file);

        card.innerHTML = `
            <div class="p-1.5 flex items-center justify-between z-10 pointer-events-none">
                <span class="bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow">
                    #${index + 1}
                </span>
            </div>
            <img src="${url}" class="absolute inset-0 w-full h-full object-cover pointer-events-none" alt="Gallery preview">
            <div class="relative z-10 p-1 bg-gradient-to-t from-black/85 via-black/50 to-transparent flex items-center justify-between gap-1 opacity-95 sm:opacity-0 group-hover:opacity-100 transition">
                <button type="button" onclick="moveGalleryCard(this, -1)" class="w-6 h-6 rounded bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center text-[10px] font-bold shadow ${index === 0 ? 'opacity-30 pointer-events-none' : ''}" title="Move Left">◀</button>
                <button type="button" onclick="removeGalleryCard(this)" class="w-6 h-6 rounded bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[11px] font-bold shadow" title="Remove image">✕</button>
                <button type="button" onclick="moveGalleryCard(this, 1)" class="w-6 h-6 rounded bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center text-[10px] font-bold shadow ${index === files.length - 1 ? 'opacity-30 pointer-events-none' : ''}" title="Move Right">▶</button>
            </div>
        `;

        card.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', index);
            card.classList.add('opacity-40');
            window._draggedGalleryCard = { block, index, maxAllowed };
        });
        card.addEventListener('dragend', () => {
            card.classList.remove('opacity-40');
            grid.querySelectorAll('.gallery-card').forEach(c => c.classList.remove('ring-2', 'ring-indigo-500'));
            window._draggedGalleryCard = null;
        });
        card.addEventListener('dragover', (e) => {
            e.preventDefault();
            card.classList.add('ring-2', 'ring-indigo-500');
        });
        card.addEventListener('dragleave', () => {
            card.classList.remove('ring-2', 'ring-indigo-500');
        });
        card.addEventListener('drop', (e) => {
            e.preventDefault();
            card.classList.remove('ring-2', 'ring-indigo-500');
            if (!window._draggedGalleryCard || window._draggedGalleryCard.block !== block) return;

            const fromIndex = window._draggedGalleryCard.index;
            const toIndex = index;
            if (fromIndex !== toIndex) {
                const movedFile = block._galleryFiles.splice(fromIndex, 1)[0];
                block._galleryFiles.splice(toIndex, 0, movedFile);
                renderGalleryPreviews(block, maxAllowed);
                syncGalleryFilesToInput(block, maxAllowed);
            }
        });

        grid.appendChild(card);
    });
}

function moveGalleryCard(btn, direction) {
    const card = btn.closest('.gallery-card');
    const block = card.closest('.color-block');
    const index = parseInt(card.dataset.index, 10);
    const newIndex = index + direction;
    const files = block._galleryFiles || [];

    if (newIndex >= 0 && newIndex < files.length) {
        const moved = files.splice(index, 1)[0];
        files.splice(newIndex, 0, moved);
        renderGalleryPreviews(block);
        syncGalleryFilesToInput(block);
    }
}
window.moveGalleryCard = moveGalleryCard;

function removeGalleryCard(btn) {
    const card = btn.closest('.gallery-card');
    const block = card.closest('.color-block');
    const index = parseInt(card.dataset.index, 10);
    const files = block._galleryFiles || [];

    if (index >= 0 && index < files.length) {
        files.splice(index, 1);
        renderGalleryPreviews(block);
        syncGalleryFilesToInput(block);
    }
}
window.removeGalleryCard = removeGalleryCard;

// ===== Bulk Select & Status Toggling in Index =====
function toggleProductStatus(id) {
    const form = document.getElementById('singleActionForm');
    if (!form) return;
    form.action = `/admin/products/${id}/toggle`;
    const methodInput = document.getElementById('singleActionMethod');
    if (methodInput) methodInput.value = 'PATCH';
    form.submit();
}
window.toggleProductStatus = toggleProductStatus;

function duplicateProduct(id, name) {
    if (confirm(`Duplicate product "${name}" with all its colors, sizes, and SKUs?`)) {
        const form = document.getElementById('singleActionForm');
        if (!form) return;
        form.action = `/admin/products/${id}/duplicate`;
        const methodInput = document.getElementById('singleActionMethod');
        if (methodInput) methodInput.value = 'POST';
        form.submit();
    }
}
window.duplicateProduct = duplicateProduct;

function deleteSingleProduct(id, name) {
    if (confirm(`Delete product "${name}" and all associated variants?`)) {
        const form = document.getElementById('singleActionForm');
        if (!form) return;
        form.action = `/admin/products/${id}`;
        const methodInput = document.getElementById('singleActionMethod');
        if (methodInput) methodInput.value = 'DELETE';
        form.submit();
    }
}
window.deleteSingleProduct = deleteSingleProduct;

function initProductBulkSelect() {
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.product-row-check');
    const bulkBar = document.getElementById('bulkBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkBar() {
        if (!bulkBar || !selectedCount) return;
        const checked = document.querySelectorAll('.product-row-check:checked');
        const count = checked.length;
        if (count > 0) {
            bulkBar.classList.remove('hidden');
            selectedCount.textContent = count;
        } else {
            bulkBar.classList.add('hidden');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });
}

// Global Delegated Listeners
document.addEventListener('DOMContentLoaded', function() {
    initProductBulkSelect();
    const colorsContainer = document.getElementById('colorsContainer');
    // On product create page: add initial empty color block if empty
    if (colorsContainer && colorsContainer.children.length === 0 && !document.querySelector('.existing-images-grid')) {
        addColorBlock();
    }
    // On product edit page: setup drag-and-drop on existing images
    document.querySelectorAll('.existing-images-grid').forEach(grid => {
        setupExistingImageDragAndDrop(grid);
    });
    document.querySelectorAll('.cover-preview-img').forEach(img => {
        if (img.getAttribute('src')) {
            img.dataset.origSrc = img.getAttribute('src');
        }
    });
});

document.addEventListener('change', function(e) {
    if (e.target && e.target.matches('.cover-file-input')) {
        handleCoverImageSelect(e.target);
    } else if (e.target && e.target.matches('.gallery-file-input')) {
        handleGalleryImagesSelect(e.target);
    }
});

document.addEventListener('avif:converted', function(e) {
    if (e.target && e.target.matches('.cover-file-input')) {
        handleCoverImageSelect(e.target);
    } else if (e.target && e.target.matches('.gallery-file-input')) {
        handleGalleryImagesSelect(e.target);
    }
});
