<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="2.0.0",
 *      title="B2B/B2C E-Commerce API Documentation",
 *      description="Comprehensive API for B2B and B2C e-commerce platform with multi-currency support, dealer management, advanced product catalog, and domain-based security. The API provides both public endpoints (for catalog browsing) and protected endpoints (for user-specific operations).",
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
 *      description="Production API Server (Domain-protected)"
 * )
 * 
 * @OA\Server(
 *      url="http://127.0.0.1:8000",
 *      description="Development API Server (Open access)"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Bearer token for authenticated endpoints (Authorization: Bearer {token})"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="domain_protection",
 *     type="apiKey",
 *     in="header",
 *     name="Origin",
 *     description="Domain-based protection via Origin header (configured domains only in production)"
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
 *     @OA\Property(property="success", type="boolean", example=false),
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
 *         @OA\Property(property="success", type="boolean", example=false),
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
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="Resource not found")
 *     )
 * )
 * 
 * @OA\Response(
 *     response="ServerError",
 *     description="Internal server error",
 *     @OA\JsonContent(
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="Internal server error"),
 *         @OA\Property(property="error", type="string", example="An unexpected error occurred")
 *     )
 * )
 * 
 * @OA\Response(
 *     response="RateLimitExceeded",
 *     description="Rate limit exceeded",
 *     @OA\JsonContent(
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="error", type="string", example="Rate limit exceeded"),
 *         @OA\Property(property="message", type="string", example="Çok fazla istek gönderiyorsunuz. Lütfen bir dakika bekleyin."),
 *         @OA\Property(property="retry_after", type="integer", example=60)
 *     )
 * )
 * 
 * @OA\Response(
 *     response="DomainNotAllowed",
 *     description="Domain not allowed (production only)",
 *     @OA\JsonContent(
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="error", type="string", example="Domain not allowed"),
 *         @OA\Property(property="message", type="string", example="Bu domain API erişimi için yetkilendirilmemiş."),
 *         @OA\Property(property="allowed_domains", type="array", @OA\Items(type="string"))
 *     )
 * )
 * 
 * @OA\Tag(
 *     name="Public API",
 *     description="Public endpoints (no authentication required) - domain protection applies in production"
 * )
 * 
 * @OA\Tag(
 *     name="Protected API", 
 *     description="Authenticated endpoints requiring Bearer token"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
