<?php

namespace App\Actions\AuditLog;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

/**
 * Action: List all activities performed by a specific user.
 *
 * Returns paginated activities (data only).
 * Controller handles Inertia rendering.
 */
class ListUserActivities
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  int  $userId  The ID of the user
     * @param  int  $perPage  Number of items per page
     * @return LengthAwarePaginator  Paginated activities
     */
    public function handle(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Activity::query()
            ->with('subject')
            ->where('causer_id', $userId)
            ->where('causer_type', 'App\\Models\\User')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
