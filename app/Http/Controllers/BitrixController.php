<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OutboundValidationRequest;
use App\Models\Log;
//use Illuminate\Support\Facades\Log;
use App\Services\OutboundService;
use http\Env\Response;
use Illuminate\Http\Request;

class BitrixController extends Controller
{
    public function receiveOutbound(OutboundValidationRequest $request)
    {
        // Make sure to still return some response messages as they can still be seen when the response is intercepted.
        // Because of this avoid returning sensitive information (like mentioning Bitrix CRM, outbound hooks, and stuff that could cause errors
        // like logging too much content in the database)
        // We will relly on HTTPS encrypting the requests and validation from our app to ensure secure communication


        // Separate deal update and contact update events
        $bitrixId = $request->data["FIELDS"]["ID"];
        $event = $request->input('event');
        switch ($event) {
            case 'ONCRMDEALUPDATE':
                Log::authLog("Successfully validated Bitrix24 Outbound Webhook! Deal ID: " . $bitrixId);
                OutboundService::handleUpdate("deal", $bitrixId);
                break;
            case 'ONCRMCONTACTUPDATE':
                Log::authLog("Successfully validated Bitrix24 Outbound Webhook! Contact ID: " . $bitrixId);
                OutboundService::handleUpdate("contact", $bitrixId);
                break;
            default:
                // Handle other or unknown events
                return response()->json(['error' => 'Unknown event.'], 422);
        }

        return "Successfully validated request.";
    }
}
