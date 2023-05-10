<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function openTest()
    {
        return response()->json(['text' => 'openTest']);
    }

    public function closeTest()
    {
        return response()->json(['text' => 'closeTest']);
    }
}
