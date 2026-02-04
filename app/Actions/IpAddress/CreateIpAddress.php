<?php

namespace App\Actions\IpAddress;

use App\Models\IpAddress;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Create a new IP address record.
 *
 * Returns the created IP address (data only).
 * Controller handles redirects and flash messages.
 */
class CreateIpAddress
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  User  $user  The user creating the IP address
     * @param  array{ip_address: string, label: string, comment?: string|null}  $data  The validated data
     * @return IpAddress  The newly created IP address
     */
    public function handle(User $user, array $data): IpAddress
    {
        return $user->ipAddresses()->create($data);
    }
}
