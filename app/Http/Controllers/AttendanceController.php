<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\TrainingAttendance;
use App\Models\TrainingCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function showProjector($id)
    {
        $course = TrainingCourse::findOrFail($id);

        $token = Str::random(32);
        Cache::put("training_token_{$id}", $token, 40);

        $url = route('attendance.verify', ['id' => $id, 'token' => $token]);
        return view('training.projector', compact('course', 'url'));
    }

    public function refreshProjectorToken($id)
    {
        TrainingCourse::findOrFail($id);

        $current = Cache::get("training_token_{$id}");
        if ($current) {
            Cache::put("training_token_old_{$id}", $current, 45);
        }

        $token = Str::random(32);
        Cache::put("training_token_{$id}", $token, 40);

        $url = route('attendance.verify', ['id' => $id, 'token' => $token]);
        $svg = (string) QrCode::format('svg')->size(400)->generate($url);

        return response()->json(['url' => $url, 'svg' => $svg]);
    }

    public function verify(Request $request, $id)
    {
        $course  = TrainingCourse::findOrFail($id);
        $token   = $request->query('token');
        $current = Cache::get("training_token_{$id}");
        $old     = Cache::get("training_token_old_{$id}");

        if (!$token || ($token !== $current && $token !== $old)) {
            return view('attendance.verify', ['expired' => true, 'course' => $course, 'token' => null]);
        }

        // Already logged in — mark attendance immediately
        if (auth()->check()) {
            $user  = auth()->user();
            $staff = Staff::where('staff_no', $user->staff_no)->first();

            if ($staff) {
                TrainingAttendance::updateOrCreate(
                    ['staff_id' => $staff->id, 'course_id' => $id],
                    [
                        'status'        => 'Completed',
                        'qr_used_at'    => now(),
                        'training_type' => $course->training_type ?? 'Internal',
                        'created_by'    => $user->id,
                    ]
                );
                session()->flash('staff_name', $user->name);
                return redirect()->route('attendance.success', ['id' => $id]);
            }
        }

        // Not logged in — show credential form
        return view('attendance.verify', ['expired' => false, 'course' => $course, 'token' => $token]);
    }

    public function verifySubmit(Request $request, $id)
    {
        $course  = TrainingCourse::findOrFail($id);
        $token   = $request->query('token');
        $current = Cache::get("training_token_{$id}");
        $old     = Cache::get("training_token_old_{$id}");

        if (!$token || ($token !== $current && $token !== $old)) {
            return view('attendance.verify', ['expired' => true, 'course' => $course, 'token' => null]);
        }

        $request->validate([
            'staff_no' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('staff_no', $request->staff_no)->where('is_active', 1)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['staff_no' => 'Invalid Staff ID or password.'])
                ->withInput(['staff_no' => $request->staff_no]);
        }

        $staff = Staff::where('staff_no', $request->staff_no)->first();
        if (!$staff) {
            return back()
                ->withErrors(['staff_no' => 'No staff record found for this Staff ID. Please contact HR.'])
                ->withInput(['staff_no' => $request->staff_no]);
        }

        TrainingAttendance::updateOrCreate(
            ['staff_id' => $staff->id, 'course_id' => $id],
            [
                'status'        => 'Completed',
                'qr_used_at'    => now(),
                'training_type' => $course->training_type ?? 'Internal',
                'created_by'    => $user->id,
            ]
        );

        session()->flash('staff_name', $user->name);
        return redirect()->route('attendance.success', ['id' => $id]);
    }

    public function success($id)
    {
        $course    = TrainingCourse::findOrFail($id);
        $staffName = session('staff_name', 'Staff');
        return view('attendance.success', ['course' => $course, 'staffName' => $staffName]);
    }
}
