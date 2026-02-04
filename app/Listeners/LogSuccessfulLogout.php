<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

/**
 * Listener to log successful user logout events.
 */
class LogSuccessfulLogout
{
    /**
     * Handle the logout event.
     *
     * @param  Logout  $event  The logout event
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $sessionId = session('audit_session_id');

            activity('authentication')
                ->causedBy($event->user)
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => $sessionId,
                ])
                ->log('User logged out');
        }
    }
}
