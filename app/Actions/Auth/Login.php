<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Authenticate user and create JWT-like token.
 *
 * Returns user and token data (data only).
 * Controller handles JSON response.
 */
class Login
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  array{email: string, password: string, device_name?: string}  $credentials
     * @return array{user: User, token: string, token_type: string, expires_at: string}
     *
     * @throws ValidationException
     */
    public function handle(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $deviceName = $credentials['device_name'] ?? 'api-token';
        $expiresAt = now()->addHours(24);
        $token = $user->createToken($deviceName, ['*'], $expiresAt);

        $this->logActivity($user);

        return [
            'user' => $user->load('roles'),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    /**
     * Log the login activity.
     *
     * @param  User  $user  The user who logged in
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
            ->log('User logged in via API');
    }
}
