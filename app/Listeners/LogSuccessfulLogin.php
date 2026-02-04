<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Str;

/**
 * Listener to log successful user login events.
 */
class LogSuccessfulLogin
{
    /**
     * Handle the login event.
     *
     * @param  Login  $event  The login event
     */
    public function handle(Login $event): void
    {
        // Generate a unique session ID for tracking activities within this session
        $sessionId = Str::uuid()->toString();
        session(['audit_session_id' => $sessionId]);

        activity('authentication')
            ->causedBy($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => $sessionId,
            ])
            ->log('User logged in');
    }
}
