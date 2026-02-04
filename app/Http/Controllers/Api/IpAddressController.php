<?php

namespace App\Http\Controllers\Api;

use App\Actions\IpAddress\AuthorizeIpAddressAction;
use App\Actions\IpAddress\CreateIpAddress;
use App\Actions\IpAddress\DeleteIpAddress;
use App\Actions\IpAddress\ListIpAddresses;
use App\Actions\IpAddress\ShowIpAddress;
use App\Actions\IpAddress\UpdateIpAddress;
use App\Actions\ReturnResponse;
use App\Http\Requests\IpAddress\StoreIpAddressRequest;
use App\Http\Requests\IpAddress\UpdateIpAddressRequest;
use App\Models\IpAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Controller for API IP address management endpoints.
 *
 * Every step is an action class - controller shows the full process flow.
 */
#[OA\Schema(
    schema: 'IpAddress',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'ip_address', type: 'string', example: '192.168.1.1'),
        new OA\Property(property: 'label', type: 'string', example: 'Main Server'),
        new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Production server'),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(
            property: 'user',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
            ]
        ),
    ]
)]
class IpAddressController extends Controller
{
    /**
     * Display a listing of IP addresses.
     *
     * @param  Request  $request  The incoming HTTP request
     * @return JsonResponse  JSON response with paginated IP addresses
     */
    #[OA\Get(
        path: '/ip-addresses',
        summary: 'List all IP addresses',
        description: 'Retrieve a paginated list of all IP addresses. All authenticated users can view all IP addresses.',
        security: [['sanctum' => []]],
        tags: ['IP Addresses'],
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                description: 'Search by IP address or label',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Number of items per page (default: 10)',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 10)
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Page number',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of IP addresses',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(ref: '#/components/schemas/IpAddress')
                                ),
                                new OA\Property(property: 'last_page', type: 'integer', example: 5),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                new OA\Property(property: 'total', type: 'integer', example: 50),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $ipAddresses = ListIpAddresses::run($request->only('search'), $request->integer('per_page', 10));

        return ReturnResponse::run(data: $ipAddresses);
    }

    /**
     * Store a newly created IP address.
     *
     * @param  StoreIpAddressRequest  $request  The validated store request
     * @return JsonResponse  JSON response with created IP address (201)
     */
    #[OA\Post(
        path: '/ip-addresses',
        summary: 'Create a new IP address',
        description: 'Add a new IP address with a label and optional comment. The IP address will be associated with the authenticated user.',
        security: [['sanctum' => []]],
        tags: ['IP Addresses'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ip_address', 'label'],
                properties: [
                    new OA\Property(property: 'ip_address', type: 'string', example: '192.168.1.100', description: 'Valid IPv4 or IPv6 address'),
                    new OA\Property(property: 'label', type: 'string', example: 'Web Server', description: 'Descriptive label for the IP'),
                    new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Production web server', description: 'Optional comment'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'IP address created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'IP address created successfully.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/IpAddress'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreIpAddressRequest $request): JsonResponse
    {
        $ipAddress = CreateIpAddress::run($request->user(), $request->validated());
        $ipAddress->load('user:id,name');

        return ReturnResponse::run(data: $ipAddress, message: 'IP address created successfully.', status: 201);
    }

    /**
     * Display the specified IP address.
     *
     * @param  IpAddress  $ipAddress  The IP address to display
     * @return JsonResponse  JSON response with IP address details
     */
    #[OA\Get(
        path: '/ip-addresses/{id}',
        summary: 'Get a specific IP address',
        description: 'Retrieve details of a specific IP address by ID.',
        security: [['sanctum' => []]],
        tags: ['IP Addresses'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'IP address ID',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'IP address details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/IpAddress'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'IP address not found'),
        ]
    )]
    public function show(IpAddress $ipAddress): JsonResponse
    {
        $data = ShowIpAddress::run($ipAddress);

        return ReturnResponse::run(data: $data);
    }

    /**
     * Update the specified IP address.
     *
     * @param  UpdateIpAddressRequest  $request  The validated update request
     * @param  IpAddress  $ipAddress  The IP address to update
     * @return JsonResponse  JSON response with updated IP address
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[OA\Put(
        path: '/ip-addresses/{id}',
        summary: 'Update an IP address',
        description: 'Update the label or comment of an IP address. Regular users can only update their own IP addresses. Super-admins can update any IP address.',
        security: [['sanctum' => []]],
        tags: ['IP Addresses'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'IP address ID',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'label', type: 'string', example: 'Updated Server Name'),
                    new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Updated comment'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'IP address updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'IP address updated successfully.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/IpAddress'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Cannot update IP addresses you do not own'),
            new OA\Response(response: 404, description: 'IP address not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateIpAddressRequest $request, IpAddress $ipAddress): JsonResponse
    {
        AuthorizeIpAddressAction::run('update', $ipAddress);
        $updated = UpdateIpAddress::run($request->user(), $ipAddress, $request->validated());
        $updated->load('user:id,name');

        return ReturnResponse::run(data: $updated, message: 'IP address updated successfully.');
    }

    /**
     * Remove the specified IP address.
     *
     * @param  IpAddress  $ipAddress  The IP address to delete
     * @return JsonResponse  JSON response confirming deletion
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[OA\Delete(
        path: '/ip-addresses/{id}',
        summary: 'Delete an IP address',
        description: 'Delete an IP address. Only super-admins can delete IP addresses. Regular users cannot delete any IP addresses.',
        security: [['sanctum' => []]],
        tags: ['IP Addresses'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'IP address ID',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'IP address deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'IP address deleted successfully.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Only super-admins can delete IP addresses'),
            new OA\Response(response: 404, description: 'IP address not found'),
        ]
    )]
    public function destroy(IpAddress $ipAddress): JsonResponse
    {
        AuthorizeIpAddressAction::run('delete', $ipAddress);
        DeleteIpAddress::run(auth()->user(), $ipAddress);

        return ReturnResponse::run(message: 'IP address deleted successfully.');
    }
}
