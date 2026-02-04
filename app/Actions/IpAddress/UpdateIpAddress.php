<?php

namespace App\Actions\IpAddress;

use App\Models\IpAddress;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Update an existing IP address record.
 *
 * Returns the updated IP address (data only).
 * Controller handles redirects and flash messages.
 */
class UpdateIpAddress
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  User  $user  The user updating the IP address
     * @param  IpAddress  $ipAddress  The IP address to update
     * @param  array{label: string, comment?: string|null}  $data  The validated data
     * @return IpAddress  The updated IP address
     */
    public function handle(User $user, IpAddress $ipAddress, array $data): IpAddress
    {
        $ipAddress->update($data);

        return $ipAddress->fresh();
    }
}
