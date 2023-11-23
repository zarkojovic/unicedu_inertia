<?php

namespace App\Jobs;

use App\Models\Log;
use CRest;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateUserContact implements ShouldQueue {

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
        $result = CRest::call("crm.contact.add", [
            'FIELDS' => [
                'NAME' => $this->user->first_name,
                'LAST_NAME' => $this->user->last_name,
                'PHONE' => [
                    [
                        'VALUE' => $this->user->phone,
                        'VALUE_TYPE' => 'OTHER',
                    ],
                ],
                'EMAIL' => [
                    [
                        'VALUE' => $this->user->email,
                        'VALUE_TYPE' => 'OTHER',
                    ],
                ],
            ],
        ]);

        //UPDATE IN DATABASE AFTER WE RECEIVE CONTACT ID
        if ($result) {
            $this->user->update(['contact_id' => $result["result"]]);
            Log::informationLog('User contant has been created!');
        }
        else {
            throw new Exception('Error has occurred while creating a deal!');
        }
    }

    public function failed($exception) {
        // This method is called when the job fails

        // Log the exception or error message
        Log::errorLog('CreateUserContact Job failed: '.$exception->getMessage());
        // Perform any necessary cleanup or notifications
    }

}
