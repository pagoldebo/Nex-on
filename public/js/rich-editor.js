// public/js/rich-editor.js
// Universal WYSIWYG & HTML Source Code Editor for Nex-on Admin Studio
// Provides robust visual formatting, table generation, and instant HTML source toggling.

(function() {
    'use strict';

    window.richEditorSavedSelections = window.richEditorSavedSelections || {};

    window.richEditorSaveSelection = function(editorId) {
        const sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            window.richEditorSavedSelections[editorId] = sel.getRangeAt(0).cloneRange();
        }
    };

    window.richEditorRestoreSelection = function(editorId) {
        const range = window.richEditorSavedSelections[editorId];
        if (range) {
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    };

    window.richEditorSetColor = function(editorId, color) {
        const canvas = document.getElementById(editorId + '_canvas');
        if (!canvas) return;
        canvas.focus();
        window.richEditorRestoreSelection(editorId);
        document.execCommand('foreColor', false, color);
        window.richEditorSync(editorId);
    };

    window.richEditorSetHighlight = function(editorId, color) {
        const canvas = document.getElementById(editorId + '_canvas');
        if (!canvas) return;
        canvas.focus();
        window.richEditorRestoreSelection(editorId);
        if (!document.execCommand('hiliteColor', false, color)) {
            document.execCommand('backColor', false, color);
        }
        window.richEditorSync(editorId);
    };

    window.richEditorSync = function(editorId) {
        const canvas = document.getElementById(editorId + '_canvas');
        const textarea = document.getElementById(editorId);
        if (canvas && textarea && !canvas.classList.contains('hidden')) {
            textarea.value = canvas.innerHTML.trim();
        }
    };

    window.richEditorExec = function(editorId, command, value = null) {
        const canvas = document.getElementById(editorId + '_canvas');
        if (!canvas) return;
        if (canvas.classList.contains('hidden')) {
            // If in HTML source view, switch back to visual first before running format
            window.richEditorToggleCode(editorId);
        }
        canvas.focus();
        document.execCommand(command, false, value);
        window.richEditorSync(editorId);
    };

    window.richEditorCreateLink = function(editorId) {
        const canvas = document.getElementById(editorId + '_canvas');
        if (!canvas) return;
        if (canvas.classList.contains('hidden')) {
            window.richEditorToggleCode(editorId);
        }
        const url = prompt('Enter the link URL (e.g. https://example.com):', 'https://');
        if (url && url !== 'https://') {
            canvas.focus();
            document.execCommand('createLink', false, url);
            window.richEditorSync(editorId);
        }
    };

    window.richEditorInsertTable = function(editorId, rows = 3, cols = 3) {
        const canvas = document.getElementById(editorId + '_canvas');
        if (!canvas) return;
        if (canvas.classList.contains('hidden')) {
            window.richEditorToggleCode(editorId);
        }
        canvas.focus();

        let tableHtml = '<table class="w-full border-collapse border border-slate-300 my-3"><thead><tr class="bg-slate-100">';
        for (let c = 1; c <= cols; c++) {
            tableHtml += `<th class="border border-slate-300 p-2 font-bold text-left">Header ${c}</th>`;
        }
        tableHtml += '</tr></thead><tbody>';
        for (let r = 1; r <= rows; r++) {
            tableHtml += '<tr>';
            for (let c = 1; c <= cols; c++) {
                tableHtml += `<td class="border border-slate-300 p-2">Item ${r}.${c}</td>`;
            }
            tableHtml += '</tr>';
        }
        tableHtml += '</tbody></table><p><br></p>';

        document.execCommand('insertHTML', false, tableHtml);
        window.richEditorSync(editorId);
    };

    window.richEditorTableAction = function(editorId, action) {
        const canvas = document.getElementById(editorId + '_canvas');
        if (!canvas) return;
        if (canvas.classList.contains('hidden')) {
            window.richEditorToggleCode(editorId);
        }
        
        // Find selected table or first table
        const sel = window.getSelection();
        let node = sel.anchorNode;
        let table = null;
        while (node && node !== canvas) {
            if (node.nodeName === 'TABLE') { table = node; break; }
            node = node.parentNode;
        }
        if (!table) {
            table = canvas.querySelector('table');
        }
        if (!table) {
            window.richEditorInsertTable(editorId, 3, 3);
            return;
        }

        if (action === 'addRow') {
            const tbody = table.querySelector('tbody') || table;
            const lastRow = tbody.querySelector('tr:last-child') || table.querySelector('tr:last-child');
            const cols = lastRow ? lastRow.children.length : 3;
            const newRow = document.createElement('tr');
            for (let i = 0; i < cols; i++) {
                const td = document.createElement('td');
                td.className = 'border border-slate-300 p-2';
                td.innerHTML = 'New data';
                newRow.appendChild(td);
            }
            tbody.appendChild(newRow);
        } else if (action === 'addCol') {
            const rows = table.querySelectorAll('tr');
            rows.forEach((row, idx) => {
                const isThead = idx === 0 && row.parentElement.nodeName === 'THEAD';
                const cell = isThead ? document.createElement('th') : document.createElement('td');
                cell.className = 'border border-slate-300 p-2' + (isThead ? ' font-bold text-left bg-slate-100' : '');
                cell.innerHTML = isThead ? 'New Col' : 'Data';
                row.appendChild(cell);
            });
        }

        window.richEditorSync(editorId);
    };

    window.richEditorToggleCode = function(editorId) {
        const wrap = document.getElementById(editorId + '_wrap') || document.querySelector(`[data-editor-id="${editorId}"]`);
        const canvas = document.getElementById(editorId + '_canvas');
        const textarea = document.getElementById(editorId);
        const toggleBtn = document.getElementById(editorId + '_toggle_btn') || (wrap ? wrap.querySelector('.rich-editor-toggle-btn') : null);

        if (!canvas || !textarea) return;

        const isCurrentlyVisual = !canvas.classList.contains('hidden') && canvas.style.display !== 'none';

        if (isCurrentlyVisual) {
            // Switch to Raw HTML Code Mode
            textarea.value = canvas.innerHTML.trim();
            canvas.classList.add('hidden');
            canvas.style.display = 'none';

            textarea.classList.remove('hidden');
            textarea.style.display = 'block';
            textarea.focus();

            if (toggleBtn) {
                toggleBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-xs', 'active');
                toggleBtn.classList.remove('bg-white', 'text-slate-600', 'border-slate-300');
                const label = toggleBtn.querySelector('.rich-editor-toggle-label');
                if (label) label.textContent = 'Visual';
                toggleBtn.setAttribute('title', 'Switch back to Visual WYSIWYG Editor');
            }

            // Dim WYSIWYG format buttons while in HTML mode
            if (wrap) {
                wrap.querySelectorAll('.rich-editor-format-btn').forEach(btn => {
                    btn.style.opacity = '0.35';
                    btn.style.pointerEvents = 'none';
                });
            }
        } else {
            // Switch back to Visual WYSIWYG Mode
            canvas.innerHTML = textarea.value;
            textarea.classList.add('hidden');
            textarea.style.display = 'none';

            canvas.classList.remove('hidden');
            canvas.style.display = 'block';
            canvas.focus();

            if (toggleBtn) {
                toggleBtn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-xs', 'active');
                toggleBtn.classList.add('bg-white', 'text-slate-600', 'border-slate-300');
                const label = toggleBtn.querySelector('.rich-editor-toggle-label');
                if (label) label.textContent = 'HTML';
                toggleBtn.setAttribute('title', 'Toggle HTML Source Code View');
            }

            // Restore format buttons
            if (wrap) {
                wrap.querySelectorAll('.rich-editor-format-btn').forEach(btn => {
                    btn.style.opacity = '';
                    btn.style.pointerEvents = '';
                });
            }

            window.richEditorSync(editorId);
        }
    };

    window.initRichEditors = function() {
        document.querySelectorAll('.rich-editor-wrapper').forEach(wrap => {
            if (wrap.dataset.initialized === 'true') return;
            wrap.dataset.initialized = 'true';
            const editorId = wrap.dataset.editorId;
            const canvas = document.getElementById(editorId + '_canvas');
            const textarea = document.getElementById(editorId);

            if (canvas && textarea) {
                canvas.addEventListener('input', () => window.richEditorSync(editorId));
                canvas.addEventListener('blur', () => window.richEditorSync(editorId));
                textarea.addEventListener('input', () => {
                    canvas.innerHTML = textarea.value;
                });

                // Ensure form submit always submits the latest editor content
                const form = wrap.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        if (!canvas.classList.contains('hidden') && canvas.style.display !== 'none') {
                            textarea.value = canvas.innerHTML.trim();
                        }
                    });
                }
            }
        });
    };

    // Auto-init on all lifecycle hooks
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initRichEditors);
    } else {
        window.initRichEditors();
    }

    window.addEventListener('pjax:complete', window.initRichEditors);
    window.addEventListener('turbo:load', window.initRichEditors);
    window.addEventListener('livewire:navigated', window.initRichEditors);

})();

