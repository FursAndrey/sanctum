<?php

namespace App\Http\Controllers;

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
