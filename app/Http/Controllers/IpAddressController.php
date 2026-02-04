<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

/**
 * Controller for managing IP addresses via web interface.
 *
 * Every step is an action class - controller shows the full process flow.
 */
class IpAddressController extends Controller
{
    /**
     * Display a listing of IP addresses.
     *
     * @param  Request  $request  The incoming HTTP request
     * @return Response  The Inertia response with paginated IP addresses
     */
    public function index(Request $request): Response
    {
        $filters = $request->only('search');
        $ipAddresses = ListIpAddresses::run($filters);

        return ReturnResponse::run(view: 'IpAddresses/Index', data: [
            'ipAddresses' => $ipAddresses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new IP address.
     *
     * @return Response  The Inertia response with create form
     */
    public function create(): Response
    {
        return ReturnResponse::run(view: 'IpAddresses/Create');
    }

    /**
     * Store a newly created IP address.
     *
     * @param  StoreIpAddressRequest  $request  The validated store request
     * @return RedirectResponse  Redirect to index with success message
     */
    public function store(StoreIpAddressRequest $request): RedirectResponse
    {
        CreateIpAddress::run($request->user(), $request->validated());

        return ReturnResponse::run(route: 'ip-addresses.index', message: 'IP address created successfully.');
    }

    /**
     * Display the specified IP address.
     *
     * @param  IpAddress  $ipAddress  The IP address to display
     * @return Response  The Inertia response with IP address details
     */
    public function show(IpAddress $ipAddress): Response
    {
        $data = ShowIpAddress::run($ipAddress);

        return ReturnResponse::run(view: 'IpAddresses/Show', data: $data);
    }

    /**
     * Show the form for editing the specified IP address.
     *
     * @param  IpAddress  $ipAddress  The IP address to edit
     * @return Response  The Inertia response with edit form
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(IpAddress $ipAddress): Response
    {
        AuthorizeIpAddressAction::run('update', $ipAddress);

        return ReturnResponse::run(view: 'IpAddresses/Edit', data: ['ipAddress' => $ipAddress]);
    }

    /**
     * Update the specified IP address.
     *
     * @param  UpdateIpAddressRequest  $request  The validated update request
     * @param  IpAddress  $ipAddress  The IP address to update
     * @return RedirectResponse  Redirect to index with success message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateIpAddressRequest $request, IpAddress $ipAddress): RedirectResponse
    {
        AuthorizeIpAddressAction::run('update', $ipAddress);
        UpdateIpAddress::run($request->user(), $ipAddress, $request->validated());

        return ReturnResponse::run(route: 'ip-addresses.index', message: 'IP address updated successfully.');
    }

    /**
     * Remove the specified IP address.
     *
     * @param  IpAddress  $ipAddress  The IP address to delete
     * @return RedirectResponse  Redirect to index with success message
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(IpAddress $ipAddress): RedirectResponse
    {
        AuthorizeIpAddressAction::run('delete', $ipAddress);
        DeleteIpAddress::run(auth()->user(), $ipAddress);

        return ReturnResponse::run(route: 'ip-addresses.index', message: 'IP address deleted successfully.');
    }
}
