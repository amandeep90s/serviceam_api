<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *    title="Swagger with Laravel",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="Admin, User and Provider Authentication APIs"
 * )
 * @OA\Tag(
 *     name="Base",
 *     description="Base APIs"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication APIs"
 * )
 * @OA\Tag(
 *     name="Common",
 *     description="Common APIs"
 * )
 * @OA\Tag(
 *     name="Transport",
 *     description="Transport Flow APIs"
 * )
 * @OA\Tag(
 *     name="Order",
 *     description="Order Flow APIs",
 * )
 * @OA\Tag(
 *     name="Service",
 *     description="Service Flow APIs"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
