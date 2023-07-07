<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *     description="This is a Task Management API",
 *     version="1.0.0",
 *     title="Task Management REST API "
 * )
 * @OA\SecurityScheme(
 *      securityScheme="apiAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
