<?php

namespace App\Actions\Auth;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Revoke the current or all tokens.
 *
 * Returns true if logout was successful (data only).
 * Controller handles JSON response.
 */
class Logout
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  User  $user  The user to logout
     * @param  bool  $revokeAll  Whether to revoke all tokens or just the current one
     * @return bool  True if logout was successful
     */
    public function handle(User $user, bool $revokeAll = false): bool
    {
        if ($revokeAll) {
            $user->tokens()->delete();
        } else {
            $user->currentAccessToken()->delete();
        }

        $this->logActivity($user);

        return true;
    }

    /**
     * Log the logout activity.
     *
     * @param  User  $user  The user who logged out
     */
    private function logActivity(User $user): void
    {
        activity('authentication')
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => 'api_token',
            ])
            ->log('User logged out via API');
    }
}
