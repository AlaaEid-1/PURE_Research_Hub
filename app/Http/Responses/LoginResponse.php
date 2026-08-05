<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect admins to the admin dashboard, researchers to their dashboard.
     */
    public function toResponse($request)
    {
        $home = auth()->user()->isAdmin()
            ? '/admin/dashboard'
            : '/dashboard';

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended($home);
    }
}
