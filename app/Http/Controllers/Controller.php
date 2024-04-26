<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *  @OA\Info(
 *      title="My API documentation",
 *      version="1.0.0",
 *  ),
 *
 *  @OA\PathItem(
 *      path="/api/"
 *  ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
