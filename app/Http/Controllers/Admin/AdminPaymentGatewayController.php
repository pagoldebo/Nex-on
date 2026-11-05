<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentGatewayController extends Controller
{
    /**
     * Display the Payment Gateways configuration hub.
     */
    public function index(): View
    {
        $gateways = [
            'razorpay' => [
                'name'           => 'Razorpay',
                'description'    => 'Accept UPI, Debit/Credit Cards, Netbanking & Wallets across India.',
                'enabled'        => Setting::get('gateway_razorpay_enabled', '0') === '1',
                'mode'           => Setting::get('gateway_razorpay_mode', 'test'), // test or live
                'key_id'         => Setting::get('gateway_razorpay_key_id', 'rzp_test_luxury12345'),
                'key_secret'     => Setting::get('gateway_razorpay_key_secret', 'sec_test_luxurySecretKey'),
                'webhook_secret' => Setting::get('gateway_razorpay_webhook_secret', 'whsec_rzpLuxury2026'),
                'webhook_url'    => url('/webhook/razorpay'),
            ],
            'stripe' => [
                'name'           => 'Stripe',
                'description'    => 'Global credit card payments, Apple Pay, Google Pay & multi-currency billing.',
                'enabled'        => Setting::get('gateway_stripe_enabled', '0') === '1',
                'mode'           => Setting::get('gateway_stripe_mode', 'test'),
                'publishable_key'=> Setting::get('gateway_stripe_publishable_key', 'pk_test_51LuxuryStoreStripeDemoKey'),
                'secret_key'     => Setting::get('gateway_stripe_secret_key', 'sk_test_51LuxuryStoreSecretDemoKey'),
                'webhook_secret' => Setting::get('gateway_stripe_webhook_secret', 'whsec_stripeLuxuryDemo'),
                'webhook_url'    => url('/webhook/stripe'),
            ],
            'phonepe' => [
                'name'           => 'PhonePe PG',
                'description'    => 'Direct Indian UPI app intent flow & QR code checkout.',
                'enabled'        => Setting::get('gateway_phonepe_enabled', '0') === '1',
                'mode'           => Setting::get('gateway_phonepe_mode', 'sandbox'),
                'merchant_id'    => Setting::get('gateway_phonepe_merchant_id', 'M22LUXURYDEMOUAT'),
                'salt_key'       => Setting::get('gateway_phonepe_salt_key', 'd3b07384d113edec49eaa6238ad5ff00'),
                'salt_index'     => Setting::get('gateway_phonepe_salt_index', '1'),
                'webhook_url'    => url('/webhook/phonepe'),
            ],
            'paypal' => [
                'name'           => 'PayPal',
                'description'    => 'International shoppers checkout with PayPal wallet & international cards.',
                'enabled'        => Setting::get('gateway_paypal_enabled', '0') === '1',
                'mode'           => Setting::get('gateway_paypal_mode', 'sandbox'),
                'client_id'      => Setting::get('gateway_paypal_client_id', 'AX_demo_luxury_paypal_client_id'),
                'secret'         => Setting::get('gateway_paypal_secret', 'EP_demo_luxury_paypal_secret'),
                'webhook_url'    => url('/webhook/paypal'),
            ],
            'offline' => [
                'enable_cod'     => Setting::get('enable_cod', '1') === '1',
                'cod_charge'     => (int) Setting::get('cod_charge', 40),
                'enable_upi_qr'  => Setting::get('enable_upi_qr', '1') === '1',
                'upi_vpa'        => Setting::get('store_upi_vpa', 'luxurystore@okhdfcbank'),
            ],
        ];

        return view('admin.settings.gateways', compact('gateways'));
    }

    /**
     * Save payment gateway credentials and switches.
     */
    public function update(Request $request): RedirectResponse
    {
        $fields = [
            // Razorpay
            'gateway_razorpay_enabled'        => $request->has('gateway_razorpay_enabled') ? '1' : '0',
            'gateway_razorpay_mode'           => $request->input('gateway_razorpay_mode', 'test'),
            'gateway_razorpay_key_id'         => $request->input('gateway_razorpay_key_id', ''),
            'gateway_razorpay_key_secret'     => $request->input('gateway_razorpay_key_secret', ''),
            'gateway_razorpay_webhook_secret' => $request->input('gateway_razorpay_webhook_secret', ''),

            // Stripe
            'gateway_stripe_enabled'          => $request->has('gateway_stripe_enabled') ? '1' : '0',
            'gateway_stripe_mode'             => $request->input('gateway_stripe_mode', 'test'),
            'gateway_stripe_publishable_key'  => $request->input('gateway_stripe_publishable_key', ''),
            'gateway_stripe_secret_key'       => $request->input('gateway_stripe_secret_key', ''),
            'gateway_stripe_webhook_secret'   => $request->input('gateway_stripe_webhook_secret', ''),

            // PhonePe
            'gateway_phonepe_enabled'         => $request->has('gateway_phonepe_enabled') ? '1' : '0',
            'gateway_phonepe_mode'            => $request->input('gateway_phonepe_mode', 'sandbox'),
            'gateway_phonepe_merchant_id'     => $request->input('gateway_phonepe_merchant_id', ''),
            'gateway_phonepe_salt_key'        => $request->input('gateway_phonepe_salt_key', ''),
            'gateway_phonepe_salt_index'      => $request->input('gateway_phonepe_salt_index', '1'),

            // PayPal
            'gateway_paypal_enabled'          => $request->has('gateway_paypal_enabled') ? '1' : '0',
            'gateway_paypal_mode'             => $request->input('gateway_paypal_mode', 'sandbox'),
            'gateway_paypal_client_id'        => $request->input('gateway_paypal_client_id', ''),
            'gateway_paypal_secret'           => $request->input('gateway_paypal_secret', ''),

            // Offline
            'enable_cod'                      => $request->has('enable_cod') ? '1' : '0',
            'cod_charge'                      => (int) $request->input('cod_charge', 40),
            'enable_upi_qr'                   => $request->has('enable_upi_qr') ? '1' : '0',
            'store_upi_vpa'                   => $request->input('store_upi_vpa', 'luxurystore@okhdfcbank'),
        ];

        foreach ($fields as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::clearCache();

        return back()->with('success', 'Payment Gateway settings and API keys updated successfully!');
    }
}
