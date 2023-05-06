<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function openTest()
    {
        return 'openTest';
    }

    public function closeTest()
    {
        return 'closeTest';
    }
}
