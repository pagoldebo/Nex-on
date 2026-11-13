<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Handle contact inquiry submission with bot honeypot, form-time check, and rate limiting.
     */
    public function submit(Request $request)
    {
        // 1. Honeypot check - if bot fills hidden website/company field, silently drop
        if ($request->filled('website') || $request->filled('company_url')) {
            Log::info('Contact form honeypot triggered by bot', ['ip' => $request->ip()]);
            return $this->respond($request, true, 'Thank you! Your message has been sent successfully.');
        }

        // 2. Timestamp check - form submission faster than 2 seconds indicates automated script
        $formTime = $request->input('form_time');
        if ($formTime && (time() * 1000 - intval($formTime)) < 2000) {
            Log::info('Contact form sub-2s automated submission detected', ['ip' => $request->ip()]);
            return $this->respond($request, true, 'Thank you! Your message has been sent successfully.');
        }

        // 3. Rate limiting - Max 5 inquiries per hour per IP
        $cacheKey = 'contact_ip_' . md5($request->ip());
        $attempts = Cache::get($cacheKey, 0);
        if ($attempts >= 5) {
            return $this->respond($request, false, 'Too many messages sent from your IP. Please try again in an hour.', 429);
        }
        Cache::put($cacheKey, $attempts + 1, now()->addHour());

        // 4. Validate user inputs
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:3000',
        ]);

        $recipientEmail = Setting::supportEmail();

        try {
            Mail::send('emails.contact-inquiry', ['data' => $validated], function ($m) use ($validated, $recipientEmail) {
                $m->to($recipientEmail)
                  ->replyTo($validated['email'], $validated['name'])
                  ->subject('Customer Inquiry: ' . ($validated['subject'] ?: 'Website Message from ' . $validated['name']));
            });
        } catch (\Exception $e) {
            Log::warning('Contact form email dispatch failed: ' . $e->getMessage());
        }

        return $this->respond($request, true, 'Thank you! Your message has been sent to our customer care team.');
    }

    private function respond(Request $request, bool $success, string $message, int $status = 200)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $status);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}
