<?php

namespace Tests\Feature;

use App\Models\PhoneOtp;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsGatewaysTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::create([
            'name'     => 'SMS Admin',
            'email'    => 'admin@sms.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    public function test_admin_can_view_sms_settings_page()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.settings.sms'));
        $response->assertStatus(200);
        $response->assertSee('Twilio');
        $response->assertSee('MSG91');
        $response->assertSee('Fast2SMS');
        $response->assertSee('Log Driver');
    }

    public function test_admin_can_save_sms_gateway_credentials()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.settings.sms.update'), [
            'sms_active_driver'                  => 'twilio',
            'sms_twilio_sid'                     => 'AC_TEST_ACCOUNT_SID',
            'sms_twilio_token'                   => 'AUTH_TOKEN_SECRET',
            'sms_twilio_from'                    => '+15550001234',
            'enable_order_confirmed_sms'         => '1',
            'enable_order_dispatched_sms'        => '1',
            'enable_otp_login_sms'               => '1',
        ]);

        $response->assertRedirect();
        $this->assertEquals('twilio', Setting::get('sms_active_driver'));
        $this->assertEquals('AC_TEST_ACCOUNT_SID', Setting::get('sms_twilio_sid'));
        $this->assertEquals('+15550001234', Setting::get('sms_twilio_from'));
    }

    public function test_admin_can_send_test_sms_via_log_driver()
    {
        $admin = $this->createAdmin();
        Setting::set('sms_active_driver', 'log');

        $response = $this->actingAs($admin)->post(route('admin.settings.sms.test'), [
            'test_phone'   => '+919876543210',
            'test_message' => 'Hello! This is a test SMS from Luxury Lingerie boutique.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sms_logs', [
            'phone'         => '+919876543210',
            'driver'        => 'log',
            'status'        => 'sent',
            'template_type' => 'test_verification',
        ]);
    }

    public function test_api_send_otp_generates_and_stores_otp()
    {
        Setting::set('sms_active_driver', 'log');

        $response = $this->postJson(route('api.sms.sendOtp'), [
            'phone' => '+919123456789',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('phone_otps', [
            'phone'       => '919123456789',
            'is_verified' => false,
        ]);
    }

    public function test_api_verify_otp_validates_correctly()
    {
        $phone = '919123456789';
        PhoneOtp::create([
            'phone'       => $phone,
            'otp'         => '654321',
            'expires_at'  => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        // 1. Wrong OTP
        $failResponse = $this->postJson(route('api.sms.verifyOtp'), [
            'phone' => '+919123456789',
            'otp'   => '000000',
        ]);
        $failResponse->assertStatus(200)
            ->assertJson([
                'success' => false,
            ]);

        // 2. Correct OTP
        $successResponse = $this->postJson(route('api.sms.verifyOtp'), [
            'phone' => '+919123456789',
            'otp'   => '654321',
        ]);
        $successResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertTrue(PhoneOtp::where('phone', $phone)->where('is_verified', true)->exists());
    }
}
