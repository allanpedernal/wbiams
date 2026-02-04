<?php

namespace App\Actions\AuditLog;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

/**
 * Action: Show a single audit log entry with full details.
 *
 * Returns the activity with loaded relations (data only).
 * Controller handles Inertia rendering.
 */
class ShowAuditLog
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  Activity  $activity  The activity to show
     * @return Activity  The activity with loaded relations
     */
    public function handle(Activity $activity): Activity
    {
        $activity->load('causer:id,name,email', 'subject');

        return $activity;
    }
}
