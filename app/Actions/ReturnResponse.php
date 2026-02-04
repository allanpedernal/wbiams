<?php

namespace App\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Intelligent response action that automatically detects response type.
 *
 * Usage:
 * - Inertia: ReturnResponse::run(view: 'Page', data: [...])
 * - Redirect: ReturnResponse::run(route: 'route.name', message: '...')
 * - JSON: ReturnResponse::run(data: [...]) or ReturnResponse::run(data: [...], message: '...')
 *
 * Detection logic:
 * 1. If 'view' is provided → Inertia response
 * 2. If 'route' is provided → Redirect response
 * 3. Otherwise → JSON response
 */
class ReturnResponse
{
    use AsAction;

    /**
     * Execute the action - intelligently returns appropriate response type.
     *
     * @param  string|null  $view  Inertia component (triggers Inertia response)
     * @param  string|null  $route  Route name (triggers redirect response)
     * @param  mixed  $data  Data for view or JSON response
     * @param  string|null  $message  Flash message (redirect) or JSON message
     * @param  array<string, mixed>  $params  Route parameters for redirect
     * @param  int  $status  HTTP status code for JSON (default 200)
     * @return InertiaResponse|RedirectResponse|JsonResponse
     */
    public function handle(
        ?string $view = null,
        ?string $route = null,
        mixed $data = null,
        ?string $message = null,
        array $params = [],
        int $status = 200,
    ): InertiaResponse|RedirectResponse|JsonResponse {
        // Inertia response - when view is provided
        if ($view !== null) {
            return $this->renderInertia($view, $data ?? []);
        }

        // Redirect response - when route is provided
        if ($route !== null) {
            return $this->redirectToRoute($route, $params, $message);
        }

        // JSON response - default
        return $this->respondWithJson($data, $message, $status);
    }

    /**
     * Render an Inertia response.
     *
     * @param  string  $view  The Inertia component name
     * @param  mixed  $data  The data to pass (array or object)
     * @return InertiaResponse
     */
    private function renderInertia(string $view, mixed $data): InertiaResponse
    {
        $props = is_array($data) ? $data : ['data' => $data];

        return Inertia::render($view, $props);
    }

    /**
     * Redirect to a route with optional flash message.
     *
     * @param  string  $route  The route name
     * @param  array<string, mixed>  $params  Route parameters
     * @param  string|null  $message  Flash message
     * @return RedirectResponse
     */
    private function redirectToRoute(string $route, array $params, ?string $message): RedirectResponse
    {
        $redirect = redirect()->route($route, $params);

        if ($message !== null) {
            $redirect->with('success', $message);
        }

        return $redirect;
    }

    /**
     * Return a JSON response.
     *
     * @param  mixed  $data  The data to return
     * @param  string|null  $message  Optional message
     * @param  int  $status  HTTP status code
     * @return JsonResponse
     */
    private function respondWithJson(mixed $data, ?string $message, int $status): JsonResponse
    {
        $response = [];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response ?: ['success' => true], $status);
    }
}
