<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Log;
//use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class BitrixController extends Controller
{
    //token for validating the outbound webhook
    private static string $applicationToken = "8nrs8p8wlywhwjo4hs866qqee1illv38";
    public function receiveOutbound(Request $request)
    {
        // Get the application token from the request
        $validated = $this->validateBitrixRequest($request->auth["application_token"]);

        if (!$validated){
            Log::errorLog("Failed to validate Bitrix24 Outbound Webhook!");
//            Log::info('Bitrix Webhook Request:', $request->all());
            return ;
        }

        Log::authLog("Successfully validated Bitrix24 Outbound Webhook!");
    }

    private function validateBitrixRequest($receivedApplicationToken)
    {
        // Compare the application token with the one generated in Bitrix24
        return $receivedApplicationToken === self::$applicationToken;
    }
}
