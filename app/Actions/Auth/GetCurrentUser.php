<?php

namespace App\Actions\Auth;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Get current authenticated user details.
 *
 * Returns user data (data only).
 * Controller handles JSON response.
 */
class GetCurrentUser
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  User  $user  The authenticated user
     * @return array{user: User, is_super_admin: bool, token_expires_at: string|null}
     */
    public function handle(User $user): array
    {
        $user->load('roles', 'permissions');

        $currentToken = $user->currentAccessToken();
        $expiresAt = $currentToken?->expires_at;

        return [
            'user' => $user,
            'is_super_admin' => $user->isSuperAdmin(),
            'token_expires_at' => $expiresAt?->toIso8601String(),
        ];
    }
}
