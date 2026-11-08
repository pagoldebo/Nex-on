{{--
    ======================================================================
    ADMIN MODULE: REVIEWS INDEX
    ======================================================================
    File: resources/views/admin/reviews/index.blade.php
    Responsibility: Back-office administration for REVIEWS INDEX
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout', [
    'title' => 'Product Reviews Moderation',
    'breadcrumb' => 'Reviews',
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showAddModal: false }">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Product Reviews & Ratings</h1>
            <p class="text-sm text-slate-500 mt-1">Moderate buyer star ratings and testimonials before publishing live to product pages and homepages.</p>
        </div>
        <div>
            <button type="button" @click="showAddModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Customer Review</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            All Reviews ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Pending Moderation ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Live / Approved ({{ $counts['approved'] }})
        </a>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Reviewer</th>
                        <th class="px-6 py-4">Rating</th>
                        <th class="px-6 py-4">Feedback</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                @if($review->product)
                                    <a href="{{ route('product.show', $review->product->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-indigo-600 transition block text-xs">
                                        {{ $review->product->name }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">Deleted Product</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800 text-xs">{{ $review->customer_name }}</p>
                                @if($review->city || $review->location)
                                    <span class="text-[10px] text-slate-500 block">{{ $review->city ?: $review->location }}</span>
                                @endif
                                @if($review->user)
                                    <span class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-semibold">Verified Buyer</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-amber-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                    <span class="ml-1 text-slate-500 font-bold text-xs">{{ $review->rating }}/5</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                @if($review->title)
                                    <p class="font-bold text-slate-900 text-xs">{{ $review->title }}</p>
                                @endif
                                <p class="text-xs text-slate-600 mt-0.5 line-clamp-2">"{{ $review->comment }}"</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $review->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $review->is_approved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60' }}">
                                    {{ $review->is_approved ? 'Live' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $review->is_approved ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/60' }} transition">
                                            {{ $review->is_approved ? 'Unpublish' : 'Approve' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No reviews found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- Add Review Modal -->
    <div x-show="showAddModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-xs"
         @keydown.escape.window="showAddModal = false">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg p-6 space-y-4"
             @click.away="showAddModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Add Customer Review</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.reviews.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Product *</label>
                    <select name="product_id" required class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-indigo-500">
                        <option value="">Select Product...</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" required placeholder="e.g. Sunita Kulkarni" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">City / Address</label>
                        <input type="text" name="city" placeholder="e.g. Mumbai" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Star Rating *</label>
                        <select name="rating" required class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-indigo-500">
                            <option value="5" selected>5 Stars (★★★★★)</option>
                            <option value="4">4 Stars (★★★★☆)</option>
                            <option value="3">3 Stars (★★★☆☆)</option>
                            <option value="2">2 Stars (★★☆☆☆)</option>
                            <option value="1">1 Star (★☆☆☆☆)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Review Title</label>
                        <input type="text" name="title" placeholder="e.g. Outstanding Purity" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Customer Feedback / Comment *</label>
                    <textarea name="comment" rows="3" required placeholder="Customer review quote or testimonial..." class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_approved" id="is_approved_chk" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="is_approved_chk" class="text-xs text-slate-700 font-medium">Publish immediately (approved for home & product pages)</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs">Save Review</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
