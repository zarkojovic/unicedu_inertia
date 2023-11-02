<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailConfirmation implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user) {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        //        $user = User::findOrFail($this->user->user_id);
        $this->user->sendEmailVerificationNotification();
        //        if ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail()) {
        //        }
    }

}
