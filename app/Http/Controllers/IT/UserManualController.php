<?php

namespace App\Http\Controllers\IT;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserManualController extends Controller
{
    public function index()
    {
        $user = auth('it')->user();
        $itRole = $user->it_role ?? 'user';
        $manuals = [];

        // Check accessibility
        $rolesToLoad = [];
        if (in_array($itRole, ['admin_it', 'admin'])) {
            $rolesToLoad = ['admin_it', 'finance_admin', 'ceo', 'gm', 'hou', 'staff'];
        } elseif ($itRole === 'finance_admin') {
            $rolesToLoad = ['finance_admin', 'staff'];
        } elseif ($itRole === 'ceo') {
            $rolesToLoad = ['ceo', 'staff'];
        } elseif ($itRole === 'gm') {
            $rolesToLoad = ['gm', 'staff'];
        } elseif ($itRole === 'hou') {
            $rolesToLoad = ['hou', 'staff'];
        } else {
            $rolesToLoad = ['staff'];
        }

        $meta = [
            'admin_it' => [
                'title' => 'Admin (IT) Manual',
                'file' => 'it_manual_admin_it.md',
                'icon' => '<i class="bi bi-shield-fill"></i>'
            ],
            'finance_admin' => [
                'title' => 'Finance Admin Manual',
                'file' => 'it_manual_finance_admin.md',
                'icon' => '<i class="bi bi-cash-coin"></i>'
            ],
            'ceo' => [
                'title' => 'CEO Manual',
                'file' => 'it_manual_ceo.md',
                'icon' => '<i class="bi bi-star-fill"></i>'
            ],
            'gm' => [
                'title' => 'GM Manual',
                'file' => 'it_manual_gm.md',
                'icon' => '<i class="bi bi-briefcase-fill"></i>'
            ],
            'hou' => [
                'title' => 'Head of Unit Manual',
                'file' => 'it_manual_hou.md',
                'icon' => '<i class="bi bi-person-badge-fill"></i>'
            ],
            'staff' => [
                'title' => 'Staff Manual',
                'file' => 'it_manual_staff.md',
                'icon' => '<i class="bi bi-person-fill"></i>'
            ],
        ];

        $flowPath = resource_path('docs/it_system_one_page_flow.md');
        if (File::exists($flowPath)) {
            $manuals['one_page_flow'] = [
                'title' => 'One Page Flow',
                'html' => Str::markdown(File::get($flowPath)),
                'icon' => '<i class="bi bi-diagram-3-fill"></i>'
            ];
        }

        foreach ($rolesToLoad as $r) {
            if (isset($meta[$r])) {
                $path = resource_path('docs/' . $meta[$r]['file']);
                if (File::exists($path)) {
                    $manuals[$r] = [
                        'title' => $meta[$r]['title'],
                        'html' => Str::markdown(File::get($path)),
                        'icon' => $meta[$r]['icon']
                    ];
                }
            }
        }

        return view('it.user_manual.index', compact('manuals'));
    }
}
