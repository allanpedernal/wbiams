<?php

namespace App\Actions\IpAddress;

use App\Models\IpAddress;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: Show a single IP address with its activity history.
 *
 * Returns the IP address and activities (data only).
 * Controller handles Inertia rendering.
 */
class ShowIpAddress
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  IpAddress  $ipAddress  The IP address to show
     * @param  int  $activityLimit  Maximum number of activities to return
     * @return array{ipAddress: IpAddress, activities: Collection}
     */
    public function handle(IpAddress $ipAddress, int $activityLimit = 50): array
    {
        $ipAddress->load('user:id,name');

        $activities = $ipAddress->activities()
            ->with('causer:id,name')
            ->latest()
            ->take($activityLimit)
            ->get();

        return [
            'ipAddress' => $ipAddress,
            'activities' => $activities,
        ];
    }
}
