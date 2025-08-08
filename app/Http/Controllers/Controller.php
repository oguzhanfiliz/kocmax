<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="B2B/B2C E-Commerce API Documentation",
 *      description="Comprehensive API for B2B and B2C e-commerce platform with multi-currency support, dealer management, and advanced product catalog.",
 *      @OA\Contact(
 *          email="support@mutfakyapim.net",
 *          name="API Support Team"
 *      ),
 *      @OA\License(
 *          name="MIT",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 * 
 * @OA\Server(
 *      url="https://b2bb2c.mutfakyapim.net",
 *      description="Production API Server"
 * )
 * 
 * @OA\Server(
 *      url="http://127.0.0.1:8000",
 *      description="Development API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Enter token in format (Bearer {token})"
 * )
 * 
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     title="Error Response",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object", 
 *         example={"field": {"This field is required."}}
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Validation Error Response",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object",
 *         @OA\Property(property="field_name", type="array", @OA\Items(type="string", example="This field is required."))
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Success Response",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully."),
 *     @OA\Property(property="data", type="object")
 * )
 * 
 * @OA\Response(
 *     response="Unauthenticated",
 *     description="Authentication required",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="Unauthenticated"),
 *         @OA\Property(property="error", type="string", example="Authentication required to access this resource")
 *     )
 * )
 * 
 * @OA\Response(
 *     response="ValidationError",
 *     description="Validation error",
 *     @OA\JsonContent(ref="#/components/schemas/ValidationError")
 * )
 * 
 * @OA\Response(
 *     response="NotFound",
 *     description="Resource not found",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="Resource not found")
 *     )
 * )
 * 
 * @OA\Response(
 *     response="ServerError",
 *     description="Internal server error",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="Internal server error"),
 *         @OA\Property(property="error", type="string", example="An unexpected error occurred")
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
