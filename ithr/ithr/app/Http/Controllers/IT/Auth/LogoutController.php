<?php

namespace App\Http\Controllers\IT\Auth;

use App\Http\Controllers\IT\Controller;
use App\Services\IT\ActivityLogService;
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

        Auth::guard('it')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('it.login');
    }
}

