<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        // Dynamically share project logo and store name across all themes and views
        View::composer('*', function ($view) {
            $view->with('siteLogo', Setting::get('site_logo'));
            $view->with('storeName', Setting::get('store_name', config('app.name', 'Nexon')));
            $view->with('organicTopNotice', Setting::get('organic_top_notice', '100% Certified Organic • Direct from Local Farmers & Sustainable Harvests'));
            $view->with('organicTopShippingNotice', Setting::get('organic_top_shipping_notice', 'Delivery to All Major Indian Pincodes'));
            $view->with('organicCustomerCare', Setting::get('organic_customer_care') ?: Setting::supportPhone());
            $view->with('organicSupportEmail', Setting::get('organic_support_email') ?: Setting::supportEmail());
            $view->with('supportPhone', Setting::supportPhone());
        });
    }
}
