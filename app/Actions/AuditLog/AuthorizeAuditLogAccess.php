<?php

namespace App\Actions\AuditLog;

use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Authorize access to audit logs (super-admin only).
 */
class AuthorizeAuditLogAccess
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle(): void
    {
        Gate::authorize('viewAuditLogs');
    }
}
