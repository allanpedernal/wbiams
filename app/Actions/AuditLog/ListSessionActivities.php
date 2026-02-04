<?php

namespace App\Actions\AuditLog;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

/**
 * Action: List all activities performed during a specific session.
 *
 * Returns paginated activities (data only).
 * Controller handles Inertia rendering.
 */
class ListSessionActivities
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  string  $sessionId  The session ID to filter by
     * @param  int  $perPage  Number of items per page
     * @return LengthAwarePaginator  Paginated activities
     */
    public function handle(string $sessionId, int $perPage = 20): LengthAwarePaginator
    {
        return Activity::query()
            ->with('causer:id,name,email', 'subject')
            ->where('properties->session_id', $sessionId)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
