<?php

namespace App\Actions\Auth;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Refresh token - issue new token and revoke old one.
 *
 * Returns user and new token data (data only).
 * Controller handles JSON response.
 */
class RefreshToken
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  User  $user  The authenticated user
     * @param  string|null  $deviceName  Optional device name for the new token
     * @return array{user: User, token: string, token_type: string, expires_at: string}
     */
    public function handle(User $user, ?string $deviceName = null): array
    {
        $currentToken = $user->currentAccessToken();
        $tokenName = $deviceName ?? $currentToken->name ?? 'api-token';

        $currentToken->delete();

        $expiresAt = now()->addHours(24);
        $newToken = $user->createToken($tokenName, ['*'], $expiresAt);

        $this->logActivity($user);

        return [
            'user' => $user->load('roles'),
            'token' => $newToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    /**
     * Log the token refresh activity.
     *
     * @param  User  $user  The user who refreshed their token
     */
    private function logActivity(User $user): void
    {
        activity('authentication')
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => 'token_refresh',
            ])
            ->log('User refreshed API token');
    }
}
