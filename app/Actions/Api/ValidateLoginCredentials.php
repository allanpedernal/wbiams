<?php

namespace App\Actions\Api;

use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Validate login credentials from request.
 */
class ValidateLoginCredentials
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  Request  $request  The incoming request
     * @return array{email: string, password: string, device_name?: string}  Validated credentials
     */
    public function handle(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
