@props([
    'name',
    'id' => null,
    'value' => '',
    'placeholder' => 'Enter description...',
    'minHeight' => '160px',
    'rows' => 5,
])

@php
    $editorId = $id ?? 'rich_editor_' . str_replace(['[', ']', '-'], '_', $name);
@endphp

<div class="rich-editor-wrapper w-full border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all"
     id="{{ $editorId }}_wrap"
     data-editor-id="{{ $editorId }}">

    {{-- Toolbar --}}
    <div class="rich-editor-toolbar bg-slate-50 border-b border-slate-200 px-3 py-2 flex flex-wrap items-center gap-1.5 select-none text-slate-700">
        
        {{-- Format Selector --}}
        <select onchange="window.richEditorExec('{{ $editorId }}', 'formatBlock', this.value); this.value='';"
                class="text-xs bg-white border border-slate-300 rounded px-2 py-1 text-slate-700 outline-none hover:border-slate-400 focus:border-indigo-500"
                title="Heading / Text Format">
            <option value="" disabled selected>Format</option>
            <option value="p">Paragraph</option>
            <option value="h3">Heading 3</option>
            <option value="h4">Heading 4</option>
            <option value="blockquote">Quote</option>
        </select>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        {{-- Bold --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'bold')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs font-bold text-slate-700 transition" 
                title="Bold (Ctrl+B)">
            <strong>B</strong>
        </button>

        {{-- Italic --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'italic')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs italic font-serif text-slate-700 transition" 
                title="Italic (Ctrl+I)">
            <em>I</em>
        </button>

        {{-- Underline --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'underline')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs underline text-slate-700 transition" 
                title="Underline (Ctrl+U)">
            <u>U</u>
        </button>

        {{-- Strike --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'strikeThrough')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs line-through text-slate-700 transition" 
                title="Strikethrough">
            <s>S</s>
        </button>

        {{-- Text Color & Highlight Dropdown --}}
        <div class="relative inline-block" x-data="{ colorMenu: false, currentColor: '#0f172a' }">
            <button type="button" 
                    @click="window.richEditorSaveSelection('{{ $editorId }}'); colorMenu = !colorMenu"
                    class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-700 transition flex items-center gap-1" 
                    title="Text & Highlight Color">
                <span class="font-black text-xs flex flex-col items-center leading-none">
                    <span>A</span>
                    <span class="w-3.5 h-1 rounded-full mt-0.5 shadow-2xs" :style="'background-color:' + currentColor"></span>
                </span>
                <svg class="w-2.5 h-2.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="colorMenu" 
                 @click.away="colorMenu = false"
                 x-transition
                 class="absolute left-0 mt-1 w-56 bg-white border border-slate-200 shadow-xl rounded-xl p-3 z-50 text-xs space-y-2.5">
                
                {{-- Text Color Header & Palette --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Text Color</span>
                        <button type="button" 
                                @mousedown.prevent="window.richEditorSetColor('{{ $editorId }}', '#0f172a'); currentColor = '#0f172a'; colorMenu = false;"
                                class="text-[10px] text-slate-500 hover:text-slate-800 font-medium">Reset</button>
                    </div>
                    <div class="grid grid-cols-5 gap-1.5">
                        @foreach([
                            ['#0f172a', 'Dark Slate'],
                            ['#475569', 'Muted Gray'],
                            ['#dc2626', 'Ruby Red'],
                            ['#e11d48', 'Rose Pink'],
                            ['#d97706', 'Warm Amber'],
                            ['#16a34a', 'Emerald Green'],
                            ['#0284c7', 'Sky Blue'],
                            ['#4f46e5', 'Indigo'],
                            ['#9333ea', 'Purple'],
                            ['#78350f', 'Bronze']
                        ] as [$cHex, $cLabel])
                            <button type="button"
                                    @mousedown.prevent="window.richEditorSetColor('{{ $editorId }}', '{{ $cHex }}'); currentColor = '{{ $cHex }}'; colorMenu = false;"
                                    class="w-7 h-7 rounded-lg border border-slate-200 hover:scale-110 hover:shadow-xs transition-transform flex items-center justify-center"
                                    style="background-color: {{ $cHex }};"
                                    title="{{ $cLabel }}">
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Color Input --}}
                <div class="border-t border-slate-100 pt-2 flex items-center justify-between">
                    <span class="text-[11px] text-slate-600 font-medium">Custom Color:</span>
                    <label class="flex items-center gap-1.5 cursor-pointer bg-slate-50 border border-slate-200 rounded px-2 py-0.5 hover:bg-slate-100">
                        <input type="color" 
                               class="w-5 h-5 rounded cursor-pointer border-0 p-0 bg-transparent"
                               @change="window.richEditorSetColor('{{ $editorId }}', $event.target.value); currentColor = $event.target.value; colorMenu = false;"
                               @input="window.richEditorSetColor('{{ $editorId }}', $event.target.value); currentColor = $event.target.value;">
                        <span class="text-[10px] font-mono text-slate-600">Pick</span>
                    </label>
                </div>

                {{-- Highlight / Background Color --}}
                <div class="border-t border-slate-100 pt-2">
                    <span class="font-bold text-slate-800 text-[11px] uppercase tracking-wider block mb-1.5">Highlight Background</span>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button"
                                @mousedown.prevent="window.richEditorSetHighlight('{{ $editorId }}', 'transparent'); colorMenu = false;"
                                class="w-6 h-6 rounded-md border border-slate-300 text-[9px] font-bold text-slate-400 hover:text-slate-700 flex items-center justify-center bg-white"
                                title="No Highlight">
                            ✕
                        </button>
                        @foreach([
                            ['#fef08a', 'Yellow'],
                            ['#bbf7d0', 'Green'],
                            ['#bfdbfe', 'Blue'],
                            ['#fbcfe8', 'Pink'],
                            ['#fed7aa', 'Orange'],
                        ] as [$hHex, $hLabel])
                            <button type="button"
                                    @mousedown.prevent="window.richEditorSetHighlight('{{ $editorId }}', '{{ $hHex }}'); colorMenu = false;"
                                    class="w-6 h-6 rounded-md border border-slate-200 hover:scale-110 transition-transform"
                                    style="background-color: {{ $hHex }};"
                                    title="{{ $hLabel }}">
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        {{-- Bullet List --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'insertUnorderedList')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-700 transition flex items-center gap-1" 
                title="Bullet Points">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
            <span class="hidden sm:inline">Bullets</span>
        </button>

        {{-- Numbered List --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'insertOrderedList')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-700 transition flex items-center gap-1" 
                title="Numbered List">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h11M9 12h11M9 19h11M5 6V3m0 0L3 4m2-1v3m-2 7h2.5a1.5 1.5 0 001.5-1.5V11a1.5 1.5 0 00-1.5-1.5H3m0 7h4v2H3v-2z"/></svg>
            <span class="hidden sm:inline">Numbers</span>
        </button>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        {{-- Table / Chart Generator Button --}}
        <div class="relative inline-block" x-data="{ tableMenu: false }">
            <button type="button" 
                    @click="tableMenu = !tableMenu"
                    class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-xs font-semibold flex items-center gap-1.5 transition"
                    title="Insert Table / Specification Chart">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-10v16m-6-16v16m12-16v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z"/></svg>
                <span>Table / Chart</span>
                <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="tableMenu" 
                 @click.away="tableMenu = false"
                 x-transition
                 class="absolute left-0 mt-1 w-56 bg-white border border-slate-200 shadow-xl rounded-xl p-2.5 z-50 text-xs space-y-1.5">
                <p class="font-bold text-slate-800 text-[11px] uppercase tracking-wider mb-1">Insert Table / Chart</p>
                <button type="button" 
                        @click="window.richEditorInsertTable('{{ $editorId }}', 3, 3); tableMenu = false;"
                        class="w-full text-left px-2.5 py-1.5 hover:bg-slate-100 rounded text-slate-700 flex items-center justify-between">
                    <span>3 × 3 Spec Chart Table</span>
                    <span class="text-[10px] text-slate-400">Default</span>
                </button>
                <button type="button" 
                        @click="window.richEditorInsertTable('{{ $editorId }}', 4, 2); tableMenu = false;"
                        class="w-full text-left px-2.5 py-1.5 hover:bg-slate-100 rounded text-slate-700 flex items-center justify-between">
                    <span>4 × 2 Features Table</span>
                    <span class="text-[10px] text-slate-400">Spec list</span>
                </button>
                <button type="button" 
                        @click="window.richEditorInsertTable('{{ $editorId }}', 5, 4); tableMenu = false;"
                        class="w-full text-left px-2.5 py-1.5 hover:bg-slate-100 rounded text-slate-700 flex items-center justify-between">
                    <span>5 × 4 Size &amp; Measure Chart</span>
                    <span class="text-[10px] text-slate-400">Size guide</span>
                </button>
                <div class="border-t border-slate-100 pt-1 mt-1 flex gap-1">
                    <button type="button" 
                            @click="window.richEditorTableAction('{{ $editorId }}', 'addRow'); tableMenu = false;"
                            class="flex-1 px-1.5 py-1 text-[11px] bg-slate-100 hover:bg-slate-200 rounded text-slate-700 font-medium text-center">
                        + Add Row
                    </button>
                    <button type="button" 
                            @click="window.richEditorTableAction('{{ $editorId }}', 'addCol'); tableMenu = false;"
                            class="flex-1 px-1.5 py-1 text-[11px] bg-slate-100 hover:bg-slate-200 rounded text-slate-700 font-medium text-center">
                        + Add Col
                    </button>
                </div>
            </div>
        </div>

        <span class="h-4 w-px bg-slate-300 mx-0.5"></span>

        {{-- Text Alignment --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'justifyLeft')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-700 transition" 
                title="Align Left">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
        </button>

        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'justifyCenter')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-700 transition" 
                title="Align Center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
        </button>

        {{-- Link --}}
        <button type="button" 
                onclick="window.richEditorCreateLink('{{ $editorId }}')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-700 transition" 
                title="Insert Link">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </button>

        {{-- Remove Formatting --}}
        <button type="button" 
                onclick="window.richEditorExec('{{ $editorId }}', 'removeFormat')" 
                class="p-1.5 hover:bg-slate-200 rounded text-xs text-slate-500 hover:text-slate-800 transition" 
                title="Clear Formatting">
            <span class="text-xs font-semibold">T<sub class="text-[9px]">x</sub></span>
        </button>

        <div class="ml-auto flex items-center gap-1.5">
            {{-- HTML Code View Toggle --}}
            <button type="button" 
                    onclick="window.richEditorToggleCode('{{ $editorId }}')" 
                    class="px-2 py-1 text-[11px] font-mono font-bold bg-white border border-slate-300 rounded text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition flex items-center gap-1"
                    title="Toggle HTML Source Code View">
                <span>&lt;/&gt;</span>
                <span class="hidden md:inline text-[10px]">HTML</span>
            </button>
        </div>

    </div>

    {{-- ContentEditable Visual Canvas --}}
    <div id="{{ $editorId }}_canvas"
         contenteditable="true"
         class="rich-editor-canvas px-4 py-3 text-sm text-slate-800 leading-relaxed outline-none overflow-y-auto"
         style="min-height: {{ $minHeight }}; max-height: 500px;"
         placeholder="{{ $placeholder }}">{!! $value !!}</div>

    {{-- Raw HTML Textarea (Synced & Submitted with Form) --}}
    <textarea name="{{ $name }}"
              id="{{ $editorId }}"
              rows="{{ $rows }}"
              class="rich-editor-raw hidden w-full px-4 py-3 font-mono text-xs text-slate-800 bg-slate-900 text-slate-100 outline-none resize-y"
              style="min-height: {{ $minHeight }};">{!! $value !!}</textarea>

</div>

@once
@push('scripts')
<style>
.rich-editor-canvas:empty:before {
    content: attr(placeholder);
    color: #94a3b8;
    pointer-events: none;
    display: block;
}
.rich-editor-canvas table {
    width: 100%;
    border-collapse: collapse;
    margin: 0.75rem 0;
    font-size: 0.875rem;
}
.rich-editor-canvas th,
.rich-editor-canvas td {
    border: 1px solid #cbd5e1;
    padding: 0.5rem 0.75rem;
    text-align: left;
}
.rich-editor-canvas th {
    background-color: #f1f5f9;
    font-weight: 700;
}
.rich-editor-canvas ul {
    list-style-type: disc;
    margin-left: 1.5rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
.rich-editor-canvas ol {
    list-style-type: decimal;
    margin-left: 1.5rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
.rich-editor-canvas li {
    margin-bottom: 0.25rem;
}
.rich-editor-canvas b,
.rich-editor-canvas strong {
    font-weight: 700;
}
.rich-editor-canvas blockquote {
    border-left: 3px solid #6366f1;
    padding-left: 0.75rem;
    color: #64748b;
    margin: 0.5rem 0;
}
</style>
<script>
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
    if (canvas && textarea) {
        textarea.value = canvas.innerHTML.trim();
    }
};

window.richEditorExec = function(editorId, command, value = null) {
    const canvas = document.getElementById(editorId + '_canvas');
    if (!canvas) return;
    canvas.focus();
    document.execCommand(command, false, value);
    window.richEditorSync(editorId);
};

window.richEditorCreateLink = function(editorId) {
    const canvas = document.getElementById(editorId + '_canvas');
    if (!canvas) return;
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
            const cell = idx === 0 && row.parentElement.nodeName === 'THEAD' ? document.createElement('th') : document.createElement('td');
            cell.className = 'border border-slate-300 p-2' + (cell.nodeName === 'TH' ? ' font-bold text-left bg-slate-100' : '');
            cell.innerHTML = cell.nodeName === 'TH' ? 'New Col' : 'Data';
            row.appendChild(cell);
        });
    }

    window.richEditorSync(editorId);
};

window.richEditorToggleCode = function(editorId) {
    const canvas = document.getElementById(editorId + '_canvas');
    const textarea = document.getElementById(editorId);
    if (!canvas || !textarea) return;

    if (canvas.classList.contains('hidden')) {
        // Switch back to Visual WYSIWYG
        canvas.innerHTML = textarea.value;
        canvas.classList.remove('hidden');
        textarea.classList.add('hidden');
    } else {
        // Switch to Raw HTML Code
        textarea.value = canvas.innerHTML.trim();
        canvas.classList.add('hidden');
        textarea.classList.remove('hidden');
        textarea.focus();
    }
};

window.initRichEditors = function() {
    document.querySelectorAll('.rich-editor-wrapper').forEach(wrap => {
        if (wrap.dataset.initialized) return;
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

            // Ensure form submit has latest content
            const form = wrap.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    if (!canvas.classList.contains('hidden')) {
                        textarea.value = canvas.innerHTML.trim();
                    }
                });
            }
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initRichEditors);
} else {
    window.initRichEditors();
}
window.addEventListener('pjax:complete', window.initRichEditors);
window.addEventListener('turbo:load', window.initRichEditors);
window.addEventListener('livewire:navigated', window.initRichEditors);
</script>
@endpush
@endonce
