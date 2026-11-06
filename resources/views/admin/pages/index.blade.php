@extends('admin.layout')

@section('title', 'Visual Block Page Builder')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-purple-50 text-purple-600 border border-purple-200/50 uppercase font-mono tracking-wider">CMS Studio</span>
                <span class="text-xs text-slate-400 font-medium">Phase 3 &bull; Modular Landing Pages</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Custom Pages &amp; Landing Microsites</h1>
            <p class="text-xs text-slate-500 mt-0.5">Build high-converting campaign landing pages, drop countdowns, and brand stories using modular drag-and-drop blocks.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pages.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create New Page</span>
            </a>
        </div>
    </div>

    <!-- Pages List Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Page Title &amp; URL</th>
                        <th class="px-6 py-4">Modular Blocks</th>
                        <th class="px-6 py-4">Visibility</th>
                        <th class="px-6 py-4">Updated At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pages as $page)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $page->title }}</div>
                                <div class="text-[11px] font-mono text-indigo-600 mt-0.5 flex items-center gap-1">
                                    <span>/p/{{ $page->slug }}</span>
                                    <a href="{{ route('custom-page.show', $page->slug) }}" target="_blank" class="hover:text-indigo-800" title="Preview live storefront page">
                                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    <span>{{ $page->blocksCount() }} Blocks Configured</span>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.pages.toggle', $page) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition {{ $page->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $page->is_published ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $page->is_published ? 'Live / Published' : 'Draft / Hidden' }}</span>
                                    </button>
                                </form>
                            </td>

                            <td class="px-6 py-4 text-slate-500 font-mono text-[11px]">
                                {{ $page->updated_at->format('d M Y, H:i') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pages.edit', $page) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold transition">
                                        Edit Blocks
                                    </a>

                                    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition" title="Delete Page">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No custom pages created yet. Click "Create New Page" above to design your first landing page!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pages->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
