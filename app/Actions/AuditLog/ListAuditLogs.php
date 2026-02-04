<?php

namespace App\Actions\AuditLog;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

/**
 * Action: List all audit logs with filtering options.
 *
 * Returns paginated activities and log names (data only).
 * Controller handles Inertia rendering.
 */
class ListAuditLogs
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  array<string, mixed>  $filters  Optional filters
     * @param  int  $perPage  Number of items per page
     * @return array{activities: LengthAwarePaginator, logNames: Collection}
     */
    public function handle(array $filters = [], int $perPage = 20): array
    {
        $activities = Activity::query()
            ->with('causer:id,name,email', 'subject')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('log_name', 'like', "%{$search}%")
                        ->orWhereHas('causer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['log_name'] ?? null, function ($query, $logName) {
                $query->where('log_name', $logName);
            })
            ->when($filters['causer_id'] ?? null, function ($query, $causerId) {
                $query->where('causer_id', $causerId);
            })
            ->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($filters['session_id'] ?? null, function ($query, $sessionId) {
                $query->where('properties->session_id', $sessionId);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $logNames = Activity::distinct()->pluck('log_name')->filter()->values();

        return [
            'activities' => $activities,
            'logNames' => $logNames,
        ];
    }
}
