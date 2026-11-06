@extends('admin.layout', [
    'title' => 'Newsletter & Marketing Campaigns',
    'breadcrumb' => 'Newsletter',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    previewModalOpen: false,
    previewHtml: '',
    loadingPreview: false,
    subject: '',
    body: '',
    async openPreview() {
        if (!this.subject.trim() || !this.body.trim()) {
            alert('Please enter both subject and email body before previewing.');
            return;
        }
        this.loadingPreview = true;
        try {
            const res = await fetch('{{ route('admin.newsletter.preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ subject: this.subject, body: this.body })
            });
            const data = await res.json();
            if (data.success) {
                this.previewHtml = data.html;
                this.previewModalOpen = true;
            }
        } catch (e) {
            alert('Failed to generate preview.');
        } finally {
            this.loadingPreview = false;
        }
    }
}">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Newsletter & Marketing Campaigns</h1>
            <p class="text-sm text-slate-500 mt-1">Broadcast new collection announcements, promotional sales, and VIP edits to your subscribers.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ number_format($totalSubscribers) }} Active Subscribers
            </span>
        </div>
    </div>

    <!-- Grid: Compose Campaign (Left) & Subscribers List (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Compose Newsletter Card (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Compose Email Broadcast
                </h2>
                <p class="text-xs text-slate-500 mt-1">Sends HTML email to all registered subscribers.</p>
            </div>

            <form action="{{ route('admin.newsletter.send') }}" method="POST" class="space-y-4" onsubmit="return confirm('Are you sure you want to broadcast this newsletter to all subscribers?')">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Subject *</label>
                    <input type="text" name="subject" x-model="subject" required placeholder="e.g. Private Access: Autumn Silk Edit & Exclusive 15% Off"
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Content / Message Body *</label>
                    <textarea name="body" x-model="body" rows="8" required placeholder="Write your newsletter announcement, story, or special coupon code here..."
                              class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 leading-relaxed"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Plain text with paragraphs will be styled into the brand email template.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" @click="openPreview()" :disabled="loadingPreview"
                            class="px-4 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span x-text="loadingPreview ? 'Generating...' : 'Live Preview'"></span>
                    </button>

                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-600/20 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Broadcast Newsletter
                    </button>
                </div>
            </form>
        </div>

        <!-- Subscribers List (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Subscribers</h2>
                <span class="text-xs text-slate-400 font-mono">{{ $subscribers->total() }} total</span>
            </div>

            <form method="GET" action="{{ route('admin.newsletter.index') }}" class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search email..."
                       class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-rose-500">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>

            <div class="overflow-y-auto max-h-[420px] divide-y divide-slate-100 pr-1">
                @forelse($subscribers as $sub)
                    <div class="py-2.5 flex items-center justify-between text-xs hover:bg-slate-50/50 rounded-lg px-2 transition">
                        <div class="min-w-0 pr-2">
                            <p class="font-medium text-slate-800 truncate">{{ $sub->email }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $sub->created_at->format('M d, Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('Unsubscribe this email?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1 rounded transition" title="Delete">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-8">No subscribers found.</p>
                @endforelse
            </div>

            @if($subscribers->hasPages())
                <div class="pt-2 border-t border-slate-100 text-xs">
                    {{ $subscribers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Live Preview Modal -->
    <div x-show="previewModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="previewModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="previewModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="previewModalOpen" x-transition.scale.95 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Email Broadcast Live Preview</span>
                    <button type="button" @click="previewModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto bg-slate-100" x-html="previewHtml"></div>
                <div class="p-3 bg-white border-t border-slate-100 flex justify-end">
                    <button type="button" @click="previewModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">Close Preview</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
