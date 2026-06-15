<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class SettingsController extends Controller
{
    public function index()
    {
        $modules = [
            'training' => 'Training Records',
            'family'   => 'Family Information',
            'rooms'    => 'Meeting Rooms',
            'requests' => 'Update Requests',
            'staff'    => 'Staff Registry'
        ];

        $settings = [];
        foreach ($modules as $key => $label) {
            $setting = SystemSetting::where('setting_key', 'module_' . $key)->first();
            $settings[$key] = $setting ? (bool)$setting->setting_value : true;
        }

        return view('settings.index', compact('modules', 'settings'));
    }

    public function store(Request $request)
    {
        $modules = ['training', 'family', 'rooms', 'requests', 'staff'];
        $changes = [];

        foreach ($modules as $m) {
            $val = $request->has('module_' . $m) ? '1' : '0';
            SystemSetting::updateOrCreate(
                ['setting_key' => 'module_' . $m],
                ['setting_value' => $val]
            );
            $changes[$m] = $val === '1' ? 'enabled' : 'disabled';
        }

        AuditLogger::log('update', 'settings',
            'Updated module visibility settings.',
            ['changes' => $changes]
        );

        return redirect()->route('settings.index')->with('success', 'Settings saved successfully.');
    }
}
