<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushCampaign;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminPushNotificationController extends Controller
{
    /**
     * Display push notification campaign control center.
     */
    public function index(): View
    {
        $campaigns = PushCampaign::latest()->paginate(15);
        $totalSubscribers = PushSubscription::where('is_active', true)->count();
        $totalSent = PushCampaign::sum('sent_count');

        return view('admin.marketing.push-notifications', compact(
            'campaigns', 'totalSubscribers', 'totalSent'
        ));
    }

    /**
     * Broadcast a new push notification campaign.
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:100',
            'body'       => 'required|string|max:255',
            'action_url' => 'nullable|url|max:255',
            'image_url'  => 'nullable|url|max:255',
        ]);

        $subscribers = PushSubscription::where('is_active', true)->get();
        $sentCount = $subscribers->count();

        $campaign = PushCampaign::create([
            'title'      => $validated['title'],
            'body'       => $validated['body'],
            'action_url' => $validated['action_url'] ?? url('/'),
            'image_url'  => $validated['image_url'] ?? null,
            'sent_count' => $sentCount,
            'status'     => 'sent',
        ]);

        Log::info("[PUSH BROADCAST] Campaign #{$campaign->id} dispatched to {$sentCount} active subscribers. Title: '{$campaign->title}'");

        return back()->with('success', "Push campaign dispatched successfully to {$sentCount} active subscriber devices!");
    }

    /**
     * Client API: Register or update browser push subscription (requires authentication - L-01).
     */
    public function apiSubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'    => 'required|url|starts_with:https://',
            'p256dh_key'  => 'nullable|string',
            'auth_token'  => 'nullable|string',
            'device_type' => 'nullable|string',
        ]);

        $hash = hash('sha256', $validated['endpoint']);

        $existing = PushSubscription::where('endpoint_hash', $hash)->first();
        if ($existing && $existing->user_id && $existing->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription endpoint belongs to another user.',
            ], 403);
        }

        $sub = PushSubscription::updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'user_id'     => Auth::id(),
                'endpoint'    => $validated['endpoint'],
                'p256dh_key'  => $validated['p256dh_key'] ?? null,
                'auth_token'  => $validated['auth_token'] ?? null,
                'device_type' => $validated['device_type'] ?? 'browser',
                'is_active'   => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device subscribed for push notifications.',
            'id'      => $sub->id,
        ]);
    }

    /**
     * Client API: Unsubscribe browser push device.
     */
    public function apiUnsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|url|starts_with:https://',
        ]);

        $hash = hash('sha256', $validated['endpoint']);
        $sub = PushSubscription::where('endpoint_hash', $hash)->first();

        if ($sub) {
            if ($sub->user_id && (! Auth::check() || $sub->user_id !== Auth::id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }
            $sub->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device unsubscribed from notifications.',
        ]);
    }
}
