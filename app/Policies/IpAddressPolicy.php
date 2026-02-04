<?php

namespace App\Policies;

use App\Models\IpAddress;
use App\Models\User;

class IpAddressPolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view all IP addresses.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * All authenticated users can view any IP address.
     */
    public function view(User $user, IpAddress $ipAddress): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * All authenticated users can create IP addresses.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Regular users can only update their own IP addresses.
     * Super-admins can update any IP address.
     */
    public function update(User $user, IpAddress $ipAddress): bool
    {
        return $user->isSuperAdmin() || $ipAddress->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Only super-admins can delete IP addresses.
     */
    public function delete(User $user, IpAddress $ipAddress): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IpAddress $ipAddress): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IpAddress $ipAddress): bool
    {
        return $user->isSuperAdmin();
    }
}
