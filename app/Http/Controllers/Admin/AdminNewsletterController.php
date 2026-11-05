<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminNewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $query  = Subscriber::latest();

        if ($search !== '') {
            $query->where('email', 'like', "%{$search}%");
        }

        $subscribers = $query->paginate(20)->withQueryString();
        $totalSubscribers = Subscriber::count();

        return view('admin.newsletter.index', compact('subscribers', 'totalSubscribers', 'search'));
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $html = view('emails.newsletter', [
            'subject'      => $validated['subject'],
            'content'      => $validated['body'],
            'customerName' => 'Valued Customer',
        ])->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $subscribers = Subscriber::all();

        if ($subscribers->isEmpty()) {
            return back()->with('error', 'No newsletter subscribers found to broadcast to.');
        }

        $sentCount = 0;
        foreach ($subscribers as $subscriber) {
            try {
                Mail::send('emails.newsletter', [
                    'subject'      => $validated['subject'],
                    'content'      => $validated['body'],
                    'customerName' => explode('@', $subscriber->email)[0],
                    'email'        => $subscriber->email,
                ], function ($message) use ($subscriber, $validated) {
                    $message->to($subscriber->email)
                            ->subject($validated['subject']);
                });
                $sentCount++;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Newsletter delivery failed for {$subscriber->email}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Newsletter dispatched successfully to {$sentCount} subscribers.");
    }

    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed successfully.');
    }
}
