<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function telegramCallback(Request $request)
    {
        $event = $request->all();
        Log::info($event);
        $event = json_decode(json_encode($event));
        Log::info($event);
    }
}
