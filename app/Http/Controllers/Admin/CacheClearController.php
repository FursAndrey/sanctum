<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheClearController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Artisan::call('optimize:clear');

        return response()->json([
            'status' => true,
            'message' => 'Cache cleared',
        ]);
    }
}
