<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for managing user password settings.
 */
class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     *
     * @return Response  The Inertia response with password settings
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     *
     * @param  PasswordUpdateRequest  $request  The validated password update request
     * @return RedirectResponse  Redirect back to password settings
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return back();
    }
}
