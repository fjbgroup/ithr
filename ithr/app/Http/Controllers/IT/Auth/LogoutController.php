<?php

namespace App\Http\Controllers\IT\Auth;

use App\Http\Controllers\IT\Controller;
use App\Services\IT\ActivityLogService;
use App\Services\SsoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $userId = Auth::guard('it')->id();
        if ($userId) {
            ActivityLogService::log('LOGOUT', 'user', $userId, 'User logged out', $userId);
        }

        SsoService::clearAuthentication();
        Auth::guard('it')->logout();
        Auth::guard('web')->logout();
        Auth::guard('wt')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

