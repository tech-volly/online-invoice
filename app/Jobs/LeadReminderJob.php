<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\LeadReminderMail;
use Mail;

class LeadReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details) {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $temp = new LeadReminderMail($this->details);
        // Mail::to($this->details['receiverEmail'])->cc(config('app.cc_admin_email'))->send($temp);
        Mail::to(config('app.cc_admin_email'))->cc($this->details['all_user_emails'])->send($temp);
    }
}
