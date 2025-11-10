<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Notifications\ConfirmationReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendConfirmationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-confirmation-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to buyers to confirm reception of their items.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = Transaction::where('status', TransactionStatus::IN_TRANSIT)
            ->where('updated_at', '<=', Carbon::now()->subDays(3))
            ->get();

        foreach ($transactions as $transaction) {
            $buyer = $transaction->offer->user;
            if ($buyer->wantsNotification('confirmation_reminder')) {
                $buyer->notify(new ConfirmationReminderNotification($transaction));
            }
        }

        $this->info('Confirmation reminders sent successfully.');
    }
}
