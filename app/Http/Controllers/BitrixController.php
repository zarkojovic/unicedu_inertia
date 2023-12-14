<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OutboundValidationRequest;
use App\Models\Log;
//use Illuminate\Support\Facades\Log;
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

        Log::authLog("Successfully validated Bitrix24 Outbound Webhook!");
        return "Successfully validated request.";
    }
}
