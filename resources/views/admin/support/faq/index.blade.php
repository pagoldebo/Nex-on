@extends('admin.layout')

@section('content')
<div class="space-y-6" x-data="{
    showFaqModal: false,
    faqMode: 'create',
    faqId: '',
    faqQuestion: '',
    faqAnswer: '',
    faqCategory: 'Shipping & Delivery',
    faqSortOrder: 0,
    faqIsActive: true,
    openCreateFaq() {
        this.faqMode = 'create';
        this.faqId = '';
        this.faqQuestion = '';
        this.faqAnswer = '';
        this.faqCategory = 'Shipping & Delivery';
        this.faqSortOrder = 0;
        this.faqIsActive = true;
        this.showFaqModal = true;
    },
    openEditFaq(id, question, answer, category, sortOrder, isActive) {
        this.faqMode = 'edit';
        this.faqId = id;
        this.faqQuestion = question;
        this.faqAnswer = answer;
        this.faqCategory = category;
        this.faqSortOrder = sortOrder || 0;
        this.faqIsActive = Boolean(isActive);
        this.showFaqModal = true;
    }
}">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">
                <span>Organization</span>
                <span>•</span>
                <span class="text-slate-500 font-medium">Domain 7: Self-Service Support</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Storefront FAQ Builder</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage customer FAQs displayed across checkout, footer help drawer, and storefront help center.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('faq') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Preview Storefront FAQ
            </a>
            <button @click="openCreateFaq()" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Question
            </button>
        </div>
    </div>

    <!-- 4 High-Impact KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Total Questions</span>
                <span class="p-2 bg-slate-50 text-slate-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($totalFaqs) }}</div>
            <p class="text-xs text-slate-500 mt-1">Managed store inquiries</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Live &amp; Active</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($activeFaqs) }}</div>
            <p class="text-xs text-slate-500 mt-1">Rendered on storefront</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Hidden / Inactive</span>
                <span class="p-2 bg-slate-50 text-slate-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-600">{{ number_format($inactiveFaqs) }}</div>
            <p class="text-xs text-slate-500 mt-1">Temporarily disabled</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-medium uppercase tracking-wider">Categories</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($categoryCount) }}</div>
            <p class="text-xs text-slate-500 mt-1">Content groupings</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.support.faq.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Search Questions &amp; Answers</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions..." 
                           class="w-full text-xs rounded-xl border-slate-200 pl-8 focus:border-rose-500 focus:ring-rose-500">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                <select name="category" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active / Live</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive / Hidden</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('admin.support.faq.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- FAQs Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">Order</th>
                        <th class="py-3.5 px-4">Question &amp; Answer Preview</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-400">
                                #{{ $faq->sort_order }}
                            </td>
                            <td class="py-3.5 px-4 max-w-lg">
                                <div class="font-bold text-slate-900 text-sm">{{ $faq->question }}</div>
                                <div class="text-slate-500 line-clamp-2 mt-1 leading-relaxed text-[11px]">
                                    {{ $faq->answer }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $faq->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.support.faq.toggle', $faq) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-semibold border transition {{ $faq->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                        {{ $faq->is_active ? 'Active' : 'Hidden' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openEditFaq({{ $faq->id }}, '{{ addslashes($faq->question) }}', '{{ addslashes(str_replace(["\r", "\n"], [' ', '\n'], $faq->answer)) }}', '{{ addslashes($faq->category) }}', {{ $faq->sort_order }}, {{ $faq->is_active ? 'true' : 'false' }})" 
                                            class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.support.faq.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ entry?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="font-semibold text-slate-600 text-sm">No FAQs match filters</p>
                                    <p class="text-xs text-slate-400">Add a new question or modify search terms.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($faqs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

    <!-- Add/Edit FAQ Modal -->
    <div x-show="showFaqModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="showFaqModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-xl bg-white rounded-3xl p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            <span x-text="faqMode === 'create' ? 'Create New Storefront FAQ' : 'Edit FAQ Entry'"></span>
                        </h3>
                        <p class="text-xs text-slate-500">Provide clear answers to customer inquiries for the storefront.</p>
                    </div>
                    <button @click="showFaqModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" :action="faqMode === 'create' ? '{{ route('admin.support.faq.store') }}' : '/admin/support/faq/' + faqId" class="mt-4 space-y-4">
                    @csrf
                    <template x-if="faqMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Question <span class="text-rose-500">*</span></label>
                        <input type="text" name="question" x-model="faqQuestion" required 
                               placeholder="e.g. How do I request a complimentary alteration?" 
                               class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                            <input type="text" name="category" x-model="faqCategory" required list="faqCategoryList" 
                                   placeholder="e.g. Shipping & Delivery" 
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                            <datalist id="faqCategoryList">
                                @foreach($categories as $c)
                                    <option value="{{ $c }}"></option>
                                @endforeach
                                <option value="Shipping & Delivery"></option>
                                <option value="Returns & Exchanges"></option>
                                <option value="Sizing & Fit"></option>
                                <option value="Atelier & Tailoring"></option>
                                <option value="Payment & Security"></option>
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Display Sort Order</label>
                            <input type="number" name="sort_order" x-model="faqSortOrder" 
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Detailed Answer <span class="text-rose-500">*</span></label>
                        <textarea name="answer" x-model="faqAnswer" rows="5" required 
                                  placeholder="Full answer text..." 
                                  class="w-full text-xs rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500"></textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="faqIsActive" 
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-semibold text-slate-800">Publish to Storefront (Active)</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" @click="showFaqModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition">
                            <span x-text="faqMode === 'create' ? 'Create Question' : 'Save Changes'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

