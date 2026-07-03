<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserManualController extends Controller
{
    /**
     * Display the User Manual dashboard with role-based document access.
     */
    public function index()
    {
        $user = Auth::user();
        $manuals = [];

        // Define role-based manual accessibility rules:
        // Admin (IT) -> Admin IT, Admin HR, Staff
        // Admin (HR) -> Admin HR, Staff
        // Others (Staff, CEO, HOU, GM, etc.) -> Staff
        $canViewIt = $user->isAdminIT();
        $canViewHr = $user->isAdminHR() || $user->isAdminIT();
        $canViewStaff = true; 

        // 1. Admin (IT) User Manual
        if ($canViewIt) {
            $path = resource_path('docs/user_manual_admin_it.md');
            if (File::exists($path)) {
                $manuals['admin_it'] = [
                    'title' => 'Admin (IT) Manual',
                    'html' => Str::markdown(File::get($path)),
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>'
                ];
            }
        }

        // 2. Admin (HR) User Manual
        if ($canViewHr) {
            $path = resource_path('docs/user_manual_admin_hr.md');
            if (File::exists($path)) {
                $manuals['admin_hr'] = [
                    'title' => 'Admin (HR) Manual',
                    'html' => Str::markdown(File::get($path)),
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
                ];
            }
        }

        // 3. Staff User Manual (all roles can view)
        if ($canViewStaff) {
            $path = resource_path('docs/user_manual_staff.md');
            if (File::exists($path)) {
                $manuals['staff'] = [
                    'title' => 'Staff Manual',
                    'html' => Str::markdown(File::get($path)),
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'
                ];
            }
        }

        return view('user_manual.index', compact('manuals'));
    }
}
