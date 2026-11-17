<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GetAdminOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:otp {email? : Optional admin email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display or generate active 2FA login OTP for admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('The admin:otp command is strictly disabled in production environments.');
            return Command::FAILURE;
        }

        $email = $this->argument('email');

        $query = User::where('role', 'admin');
        if ($email) {
            $admin = $query->where('email', $email)->first();
        } else {
            $admin = (clone $query)->whereNotNull('otp_code')
                ->where('otp_expires_at', '>', now())
                ->latest('updated_at')
                ->first() ?: $query->where('email', 'admin@nexon.app')->first() ?: $query->first();
        }

        if (!$admin) {
            $this->error('No admin account found.');
            return Command::FAILURE;
        }

        $plainOtp = $admin->generateOtp(10);
        $this->info("Generated fresh 10-minute OTP for {$admin->email}:");

        $this->newLine();
        $this->line("  🔑 Admin Email : <fg=cyan>{$admin->email}</>");
        $this->line("  ⚡ OTP Code    : <fg=green;options=bold>{$plainOtp}</>");
        $this->line("  ⏳ Expires At  : <fg=yellow>{$admin->fresh()->otp_expires_at}</>");
        $this->newLine();

        return Command::SUCCESS;
    }
}

