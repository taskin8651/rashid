<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailyReportReminderNotification;
use Illuminate\Console\Command;

class RemindMissingDailyReports extends Command
{
    protected $signature = 'daily-reports:remind';

    protected $description = 'Notify staff/teacher users who have not submitted a daily work report for today';

    public function handle(): void
    {
        $reminded = 0;

        User::role(['staff', 'teacher'])->where('is_active', true)->get()
            ->reject(fn (User $user) => $user->dailyReports()->whereDate('report_date', today())->exists())
            ->each(function (User $user) use (&$reminded) {
                $user->notify(new DailyReportReminderNotification());
                $reminded++;
            });

        $this->info("Sent {$reminded} daily report reminder(s).");
    }
}
