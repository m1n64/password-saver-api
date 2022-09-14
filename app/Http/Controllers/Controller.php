<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Password Saver REST API",
 *      description="Password saver api",
 * )
 * @OA\Server(url="/api")
 * @OA\Tag(
 *     name="Authentification",
 *     description="API Endpoints for Auth"
 * )
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for Categories"
 * )
 * @OA\Tag(
 *     name="Passwords",
 *     description="API Endpoints for Passwords"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 *
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
