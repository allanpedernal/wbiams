<?php

namespace App\Actions\IpAddress;

use App\Models\IpAddress;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Authorize an action on an IP address.
 */
class AuthorizeIpAddressAction
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  string  $ability  The ability to check (update, delete)
     * @param  IpAddress  $ipAddress  The IP address to authorize against
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle(string $ability, IpAddress $ipAddress): void
    {
        Gate::authorize($ability, $ipAddress);
    }
}
