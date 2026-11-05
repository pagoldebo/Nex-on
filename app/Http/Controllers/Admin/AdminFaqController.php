<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    /**
     * Job 28: Storefront Frequently Asked Questions (FAQ) Builder.
     */
    public function index(Request $request)
    {
        $query = Faq::query()->orderBy('category')->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('question', 'LIKE', "%{$s}%")
                    ->orWhere('answer', 'LIKE', "%{$s}%")
                    ->orWhere('category', 'LIKE', "%{$s}%");
            });
        }

        $faqs = $query->paginate(20)->withQueryString();

        $categories = Faq::select('category')->distinct()->pluck('category');

        $totalFaqs      = Faq::count();
        $activeFaqs     = Faq::where('is_active', true)->count();
        $inactiveFaqs   = $totalFaqs - $activeFaqs;
        $categoryCount  = $categories->count();

        return view('admin.support.faq.index', compact(
            'faqs',
            'categories',
            'totalFaqs',
            'activeFaqs',
            'inactiveFaqs',
            'categoryCount'
        ));
    }

    /**
     * Store new FAQ.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string',
            'category'   => 'required|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $faq = Faq::create([
            'question'   => $validated['question'],
            'answer'     => $validated['answer'],
            'category'   => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => (bool) ($request->is_active ?? true),
        ]);

        return redirect()->route('admin.support.faq.index')
            ->with('success', "FAQ '{$faq->question}' created.");
    }

    /**
     * Update existing FAQ.
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question'   => 'required|string|max:255',
            'answer'     => 'required|string',
            'category'   => 'required|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $faq->update([
            'question'   => $validated['question'],
            'answer'     => $validated['answer'],
            'category'   => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? $faq->sort_order,
            'is_active'  => (bool) ($request->is_active ?? false),
        ]);

        return redirect()->route('admin.support.faq.index')
            ->with('success', "FAQ updated successfully.");
    }

    /**
     * Delete FAQ.
     */
    public function destroy(Faq $faq)
    {
        $question = $faq->question;
        $faq->delete();

        return redirect()->route('admin.support.faq.index')
            ->with('success', "FAQ '{$question}' deleted.");
    }

    /**
     * Toggle active state.
     */
    public function toggle(Faq $faq)
    {
        $faq->is_active = !$faq->is_active;
        $faq->save();

        $status = $faq->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "FAQ is now {$status}.");
    }
}

