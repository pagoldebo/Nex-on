<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');

        $query = ProductReview::with(['product', 'user'])->latest();

        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        }

        $reviews = $query->paginate(15)->withQueryString();

        $counts = [
            'all'      => ProductReview::count(),
            'pending'  => ProductReview::where('is_approved', false)->count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
        ];

        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'status', 'counts', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'customer_name' => 'required|string|max:100',
            'city'          => 'nullable|string|max:100',
            'rating'        => 'required|integer|between:1,5',
            'title'         => 'nullable|string|max:150',
            'comment'       => 'required|string|max:1000',
            'is_approved'   => 'nullable|boolean',
        ]);

        ProductReview::create([
            'product_id'    => $validated['product_id'],
            'customer_name' => $validated['customer_name'],
            'city'          => $validated['city'] ?? null,
            'rating'        => $validated['rating'],
            'title'         => $validated['title'] ?? null,
            'comment'       => $validated['comment'],
            'is_approved'   => $request->boolean('is_approved', true),
        ]);

        return back()->with('success', 'Customer review added successfully.');
    }

    public function toggle(ProductReview $review): RedirectResponse
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', $review->is_approved ? 'Review approved and published.' : 'Review unpublished.');
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
