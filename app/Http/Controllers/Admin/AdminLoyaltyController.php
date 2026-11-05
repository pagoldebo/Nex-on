<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLoyaltyController extends Controller
{
    /**
     * Display loyalty program dashboard, VIP tiers, and members directory.
     */
    public function index(Request $request): View
    {
        $tierId = $request->input('tier_id');
        $search = $request->input('search');

        // Automatically ensure existing customers have a loyalty account if not already created
        $customersWithoutLoyalty = User::where('role', 'customer')
            ->doesntHave('loyaltyAccount')
            ->get();

        $defaultTier = LoyaltyTier::where('slug', 'silver')->first() ?? LoyaltyTier::first();

        if ($defaultTier) {
            foreach ($customersWithoutLoyalty as $customer) {
                LoyaltyAccount::create([
                    'user_id'         => $customer->id,
                    'loyalty_tier_id' => $defaultTier->id,
                    'points_balance'  => 50, // Welcome bonus points
                    'points_earned_lifetime' => 50,
                ]);
            }
        }

        $query = LoyaltyAccount::with(['user', 'tier']);

        if ($tierId) {
            $query->where('loyalty_tier_id', $tierId);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('points_balance', 'desc')->paginate(15)->withQueryString();

        // High-level KPI aggregations
        $allAccounts = LoyaltyAccount::all();
        $totalMembers = $allAccounts->count();
        $totalPointsCirculation = (int) $allAccounts->sum('points_balance');
        $totalPointsLiabilityRupees = $totalPointsCirculation * 0.50; // 1 pt = ₹0.50
        $totalPointsRedeemed = (int) $allAccounts->sum('points_redeemed_lifetime');

        // Tiers with active member counts
        $tiers = LoyaltyTier::withCount('accounts')->orderBy('min_spend', 'asc')->get();

        return view('admin.marketing.loyalty', compact(
            'accounts',
            'tiers',
            'totalMembers',
            'totalPointsCirculation',
            'totalPointsLiabilityRupees',
            'totalPointsRedeemed',
            'tierId',
            'search'
        ));
    }

    /**
     * Update loyalty tier rules and perks.
     */
    public function updateTier(Request $request, LoyaltyTier $tier): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:50',
            'min_spend'         => 'required|numeric|min:0',
            'points_multiplier' => 'required|numeric|min:1|max:10',
            'perks'             => 'nullable|string|max:500',
            'badge_color'       => 'nullable|string|max:20',
        ]);

        $tier->update($validated);

        return back()->with('success', "Loyalty Tier '{$tier->name}' updated successfully.");
    }

    /**
     * Adjust member points balance (credit bonus points or deduct points).
     */
    public function adjustPoints(Request $request, LoyaltyAccount $account): RedirectResponse
    {
        $validated = $request->validate([
            'points'      => 'required|integer',
            'description' => 'required|string|max:255',
        ]);

        $account->adjustPoints($validated['points'], $validated['description']);

        $direction = $validated['points'] >= 0 ? 'credited to' : 'deducted from';
        return back()->with('success', abs($validated['points']) . " points {$direction} {$account->user->name}'s balance.");
    }
}

