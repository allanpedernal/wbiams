<?php

namespace App\Actions\IpAddress;

use App\Models\IpAddress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Action: List all IP addresses with optional search filtering.
 *
 * Returns paginated IP addresses (data only).
 * Controller handles Inertia rendering or JSON response.
 */
class ListIpAddresses
{
    use AsAction;

    /**
     * Execute the action.
     *
     * @param  array<string, mixed>  $filters  Optional filters (search)
     * @param  int  $perPage  Number of items per page
     * @return LengthAwarePaginator  Paginated IP addresses
     */
    public function handle(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return IpAddress::query()
            ->with('user:id,name')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ip_address', 'like', "%{$search}%")
                        ->orWhere('label', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
