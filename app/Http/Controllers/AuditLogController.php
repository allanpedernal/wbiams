<?php

namespace App\Http\Controllers;

use App\Actions\AuditLog\AuthorizeAuditLogAccess;
use App\Actions\AuditLog\ListAuditLogs;
use App\Actions\AuditLog\ListIpAddressHistory;
use App\Actions\AuditLog\ListSessionActivities;
use App\Actions\AuditLog\ListUserActivities;
use App\Actions\AuditLog\ShowAuditLog;
use App\Actions\ReturnResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

/**
 * Controller for managing audit logs via web interface.
 *
 * Every step is an action class - controller shows the full process flow.
 * All methods require super-admin access.
 */
class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     *
     * @param  Request  $request  The incoming HTTP request
     * @return Response  The Inertia response with paginated audit logs
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request): Response
    {
        AuthorizeAuditLogAccess::run();

        $filters = $request->only(['search', 'log_name', 'causer_id', 'date_from', 'date_to', 'session_id']);
        $data = ListAuditLogs::run($filters);

        return ReturnResponse::run(view: 'AuditLogs/Index', data: [
            'activities' => $data['activities'],
            'logNames' => $data['logNames'],
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified audit log entry.
     *
     * @param  Activity  $activity  The activity to display
     * @return Response  The Inertia response with activity details
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Activity $activity): Response
    {
        AuthorizeAuditLogAccess::run();

        $activityData = ShowAuditLog::run($activity);

        return ReturnResponse::run(view: 'AuditLogs/Show', data: ['activity' => $activityData]);
    }

    /**
     * Display activities for a specific user.
     *
     * @param  int  $userId  The ID of the user
     * @return Response  The Inertia response with user activities
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function userActivities(int $userId): Response
    {
        AuthorizeAuditLogAccess::run();

        $activities = ListUserActivities::run($userId);

        return ReturnResponse::run(view: 'AuditLogs/UserActivities', data: [
            'activities' => $activities,
            'userId' => $userId,
        ]);
    }

    /**
     * Display activity history for a specific IP address.
     *
     * @param  int  $ipAddressId  The ID of the IP address
     * @return Response  The Inertia response with IP address history
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function ipAddressHistory(int $ipAddressId): Response
    {
        AuthorizeAuditLogAccess::run();

        $activities = ListIpAddressHistory::run($ipAddressId);

        return ReturnResponse::run(view: 'AuditLogs/IpAddressHistory', data: [
            'activities' => $activities,
            'ipAddressId' => $ipAddressId,
        ]);
    }

    /**
     * Display activities for a specific session.
     *
     * @param  string  $sessionId  The session ID
     * @return Response  The Inertia response with session activities
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sessionActivities(string $sessionId): Response
    {
        AuthorizeAuditLogAccess::run();

        $activities = ListSessionActivities::run($sessionId);

        return ReturnResponse::run(view: 'AuditLogs/SessionActivities', data: [
            'activities' => $activities,
            'sessionId' => $sessionId,
        ]);
    }
}
