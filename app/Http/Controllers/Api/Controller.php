<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

/**
 * Base API Controller with OpenAPI documentation.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'WBIAMS API Documentation',
    description: 'Web-Based IP Address Management System API. Provides endpoints for authentication and IP address management with role-based access control.',
    contact: new OA\Contact(
        name: 'API Support',
        email: 'support@wbiams.local'
    )
)]
#[OA\Server(
    url: '/api',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Laravel Sanctum Bearer Token. Login via POST /api/auth/login to obtain a token.'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'API endpoints for user authentication'
)]
#[OA\Tag(
    name: 'IP Addresses',
    description: 'API endpoints for IP address management'
)]
abstract class Controller extends \App\Http\Controllers\Controller
{
}
