<?php

namespace App\Http\Controllers\Api;

use App\Actions\Api\ValidateLoginCredentials;
use App\Actions\Auth\GetCurrentUser;
use App\Actions\Auth\Login;
use App\Actions\Auth\Logout;
use App\Actions\Auth\RefreshToken;
use App\Actions\ReturnResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Controller for API authentication endpoints.
 *
 * Every step is an action class - controller shows the full process flow.
 */
class AuthController extends Controller
{
    /**
     * Authenticate user and return a token.
     *
     * @param  Request  $request  The incoming HTTP request with credentials
     * @return JsonResponse  JSON response with user data and token
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    #[OA\Post(
        path: '/auth/login',
        summary: 'Authenticate user',
        description: 'Login with email and password to receive an authentication token.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'postman', description: 'Optional device name for token identification'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
                                        new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), example: ['super-admin']),
                                    ]
                                ),
                                new OA\Property(property: 'token', type: 'string', example: '1|abc123xyz...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The email field is required.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $credentials = ValidateLoginCredentials::run($request);
        $data = Login::run($credentials);

        return ReturnResponse::run(data: $data, message: 'Login successful.');
    }

    /**
     * Get the authenticated user.
     *
     * @param  Request  $request  The incoming HTTP request
     * @return JsonResponse  JSON response with user data
     */
    #[OA\Get(
        path: '/auth/user',
        summary: 'Get current user',
        description: 'Retrieve the currently authenticated user information.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
                                new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), example: ['super-admin']),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function user(Request $request): JsonResponse
    {
        $data = GetCurrentUser::run($request->user());

        return ReturnResponse::run(data: $data);
    }

    /**
     * Refresh the authentication token.
     *
     * @param  Request  $request  The incoming HTTP request
     * @return JsonResponse  JSON response with new token
     */
    #[OA\Post(
        path: '/auth/refresh',
        summary: 'Refresh authentication token',
        description: 'Revoke current token and issue a new one to prevent session expiration.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'device_name', type: 'string', example: 'postman', description: 'Optional device name for new token'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token refreshed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Token refreshed successfully.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '2|newtoken123...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function refresh(Request $request): JsonResponse
    {
        $data = RefreshToken::run($request->user(), $request->input('device_name'));

        return ReturnResponse::run(data: $data, message: 'Token refreshed successfully.');
    }

    /**
     * Logout the user and revoke token.
     *
     * @param  Request  $request  The incoming HTTP request
     * @return JsonResponse  JSON response confirming logout
     */
    #[OA\Post(
        path: '/auth/logout',
        summary: 'Logout user',
        description: 'Revoke the current authentication token. Optionally revoke all tokens.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'revoke_all', type: 'boolean', example: false, description: 'Set to true to revoke all user tokens'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logout successful.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        Logout::run($request->user(), $request->boolean('revoke_all', false));

        return ReturnResponse::run(message: 'Logout successful.');
    }
}
