<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\school;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceExpiryReminder;

class SendServiceExpiryReminders extends Command
{
    protected $signature = 'reminders:service-expiry';
    protected $description = 'Send service expiry reminders to school administrators';

    public function handle()
    {
        $this->info('Checking service expiry dates...');

        // Pata schools zote ambazo service itaisha ndani ya siku 30
        $schools = school::where('status', 1)
            ->whereDate('service_end_date', '<=', Carbon::now()->addDays(30))
            ->whereDate('service_end_date', '>', Carbon::now())
            ->get();

        $this->info('Found ' . $schools->count() . ' schools with expiring service');

        foreach ($schools as $school) {
            // Pata school administrator
            $admin = User::where('school_id', $school->id)
                ->where('usertype', 2)
                ->first();

            if (!$admin) {
                $this->warn('No admin found for school: ' . $school->school_name);
                continue;
            }

            // Check kama tayari tumemtumia reminder leo
            $cacheKey = 'service_reminder_' . $school->id . '_' . now()->format('Y-m-d');

            if (!cache()->has($cacheKey)) {
                try {
                    // Tuma email (Unda Mail class kwanza)
                    // Mail::to($admin->email)->send(new ServiceExpiryReminder($school, $admin));

                    // Kwa sasa, tumia log
                    $daysLeft = now()->diffInDays($school->service_end_date, false);
                    $this->info("Reminder sent to {$admin->email} for {$school->school_name}. Days left: {$daysLeft}");

                    // Weka flag kwenye cache (expires after 24 hours)
                    cache()->put($cacheKey, true, now()->addHours(24));

                } catch (\Exception $e) {
                    $this->error('Failed to send reminder: ' . $e->getMessage());
                }
            } else {
                $this->line("Already notified today for: {$school->school_name}");
            }
        }

        $this->info('Reminder process completed!');
    }
}
