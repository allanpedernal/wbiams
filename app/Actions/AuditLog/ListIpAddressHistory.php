<?php

namespace App\Actions\AuditLog;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

/**
 * Action: List complete activity history for a specific IP address.
 *
 * Returns paginated activities (data only).
 * Controller handles Inertia rendering.
 */
class ListIpAddressHistory
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  int  $ipAddressId  The ID of the IP address
     * @param  int  $perPage  Number of items per page
     * @return LengthAwarePaginator  Paginated activities
     */
    public function handle(int $ipAddressId, int $perPage = 20): LengthAwarePaginator
    {
        return Activity::query()
            ->with('causer:id,name,email')
            ->where('subject_id', $ipAddressId)
            ->where('subject_type', 'App\\Models\\IpAddress')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
