<?php

namespace App\Actions\IpAddress;

use App\Models\IpAddress;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Delete an IP address record.
 *
 * Returns true if deleted successfully (data only).
 * Controller handles redirects and flash messages.
 */
class DeleteIpAddress
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  User  $user  The user deleting the IP address
     * @param  IpAddress  $ipAddress  The IP address to delete
     * @return bool  True if deletion was successful
     */
    public function handle(User $user, IpAddress $ipAddress): bool
    {
        return $ipAddress->delete();
    }
}
