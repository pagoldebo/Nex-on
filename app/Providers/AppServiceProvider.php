<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        $this->configureRateLimiting();

        // Enforce centralized authorization checks for all system permissions
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
            return null;
        });

        // Enforce application-wide robust password security defaults
        Password::defaults(function () {
            $rule = Password::min(8);
            return app()->isProduction()
                ? $rule->mixedCase()->numbers()->symbols()->uncompromised()
                : $rule;
        });

        // Dynamically share project logo and store name across all themes and views
        View::composer('*', function ($view) {
            $data = $view->getData();
            if (!array_key_exists('siteLogo', $data)) {
                $view->with('siteLogo', Setting::get('site_logo'));
            }
            if (!array_key_exists('siteLogoUrl', $data)) {
                $view->with('siteLogoUrl', Setting::siteLogoUrl());
            }
            if (!array_key_exists('storeName', $data)) {
                $view->with('storeName', Setting::get('store_name', config('app.name', 'Nexon')));
            }
            if (!array_key_exists('organicTopNotice', $data)) {
                $view->with('organicTopNotice', Setting::get('organic_top_notice', '100% Certified Organic • Direct from Local Farmers & Sustainable Harvests'));
            }
            if (!array_key_exists('organicTopShippingNotice', $data)) {
                $view->with('organicTopShippingNotice', Setting::get('organic_top_shipping_notice', 'Delivery to All Major Indian Pincodes'));
            }
            if (!array_key_exists('organicCustomerCare', $data)) {
                $view->with('organicCustomerCare', Setting::get('organic_customer_care') ?: Setting::supportPhone());
            }
            if (!array_key_exists('organicSupportEmail', $data)) {
                $view->with('organicSupportEmail', Setting::supportEmail());
            }
            if (!array_key_exists('supportEmail', $data)) {
                $view->with('supportEmail', Setting::supportEmail());
            }
            if (!array_key_exists('supportPhone', $data)) {
                $view->with('supportPhone', Setting::supportPhone());
            }
        });
    }

    /**
     * Configure named rate limiters across security-sensitive application vectors.
     */
    protected function configureRateLimiting(): void
    {
        // 1. Authentication & Identity Protection
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');
            $throttleKey = Str::transliterate(Str::lower($email) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

        RateLimiter::for('otp_verify', function (Request $request) {
            $key = $request->session()->get('pending_2fa_user_id') ?: $request->ip();
            return Limit::perMinutes(5, 5)->by($key);
        });

        RateLimiter::for('otp_resend', function (Request $request) {
            $key = $request->session()->get('pending_2fa_user_id') ?: $request->ip();
            return Limit::perMinutes(5, 3)->by($key);
        });

        RateLimiter::for('password_reset', function (Request $request) {
            $email = (string) $request->input('email', '');
            $throttleKey = Str::transliterate(Str::lower($email) . '|' . $request->ip());
            return Limit::perMinutes(15, 5)->by($throttleKey);
        });

        // 2. SMS Gateway & Phone OTP Protection
        RateLimiter::for('sms_send', function (Request $request) {
            $phone = preg_replace('/[^0-9]/', '', (string) $request->input('phone', ''));
            $key = ($phone ?: 'anon') . '|' . $request->ip();
            return Limit::perMinutes(10, 5)->by($key);
        });

        RateLimiter::for('sms_verify', function (Request $request) {
            $phone = preg_replace('/[^0-9]/', '', (string) $request->input('phone', ''));
            $key = ($phone ?: 'anon') . '|' . $request->ip();
            return Limit::perMinutes(5, 5)->by($key);
        });

        // 3. Storefront Transactional & Commerce Protection
        RateLimiter::for('checkout', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return Limit::perMinutes(5, 10)->by($key);
        });

        RateLimiter::for('coupon_apply', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('reviews', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return Limit::perMinutes(5, 5)->by($key);
        });

        RateLimiter::for('newsletter', function (Request $request) {
            return Limit::perMinutes(5, 5)->by($request->ip());
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('order_request', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return Limit::perMinutes(5, 6)->by($key);
        });

        RateLimiter::for('payment_verify', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

        RateLimiter::for('pincode_check', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('push_subscription', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('driver_status', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
