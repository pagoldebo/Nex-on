<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = DB::table('settings')
            ->where('value', 'LIKE', '%nexon.test%')
            ->orWhere('value', 'LIKE', 'http://%')
            ->orWhere('value', 'LIKE', 'https://%')
            ->get();

        foreach ($settings as $setting) {
            if (is_string($setting->value)) {
                $clean = Setting::relativizeUrl($setting->value);
                if ($clean !== $setting->value) {
                    DB::table('settings')
                        ->where('id', $setting->id)
                        ->update(['value' => $clean]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for relativizing URLs
    }
};
