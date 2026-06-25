<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\TrainingAttendance;
use App\Models\TrainingCourse;
use App\Models\TrainingFeedback;
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

    public function scan()
    {
        // Public camera-based QR reader. Decodes the projector QR (an
        // attendance.verify URL) client-side and redirects the phone to it.
        return view('attendance.scan');
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

            if ($staff && !$staff->is_active) {
                return view('attendance.verify', [
                    'expired'    => false,
                    'course'     => $course,
                    'token'      => $token,
                    'inactive'   => true,
                ]);
            }

            if ($staff) {
                $attendance = TrainingAttendance::updateOrCreate(
                    ['staff_id' => $staff->id, 'course_id' => $id],
                    [
                        'status'        => 'Completed',
                        'qr_used_at'    => now(),
                        'training_type' => $course->training_type ?? 'Internal',
                        'created_by'    => $user->id,
                    ]
                );
                session()->flash('staff_name', $user->name);
                session()->flash('attendance_id', $attendance->id);
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

        if (!$staff->is_active) {
            return back()
                ->withErrors(['staff_no' => 'Your account is currently inactive. You cannot mark training attendance. Please contact HR.'])
                ->withInput(['staff_no' => $request->staff_no]);
        }

        // The staff just proved their credentials on the QR verify form, so log
        // them in. This keeps the session authenticated for the success page so
        // "View My Training" goes straight to the training page without a second login.
        auth()->login($user);
        $request->session()->regenerate();

        $attendance = TrainingAttendance::updateOrCreate(
            ['staff_id' => $staff->id, 'course_id' => $id],
            [
                'status'        => 'Completed',
                'qr_used_at'    => now(),
                'training_type' => $course->training_type ?? 'Internal',
                'created_by'    => $user->id,
            ]
        );

        session()->flash('staff_name', $user->name);
        session()->flash('attendance_id', $attendance->id);
        return redirect()->route('attendance.success', ['id' => $id]);
    }

    public function success($id)
    {
        $course    = TrainingCourse::findOrFail($id);
        $staffName = session('staff_name', 'Staff');

        // The attendance row just marked — drives the feedback questionnaire prompt.
        // Falls back to old() so the form survives a validation-error redirect.
        $attendance = null;
        $attendanceId = session('attendance_id') ?? old('attendance_id');
        if ($attendanceId) {
            $attendance = TrainingAttendance::with('feedback')
                ->where('id', $attendanceId)
                ->where('course_id', $id)
                ->first();
            // Keep it available across a refresh of the success page.
            session()->flash('attendance_id', $attendanceId);
        }

        return view('attendance.success', [
            'course'     => $course,
            'staffName'  => $staffName,
            'attendance' => $attendance,
        ]);
    }

    public function feedbackStore(Request $request, $id)
    {
        $course = TrainingCourse::findOrFail($id);

        $validated = $request->validate([
            'attendance_id'   => 'required|exists:training_attendances,id',
            'content_rating'  => 'required|integer|min:1|max:5',
            'trainer_rating'  => 'required|integer|min:1|max:5',
            'venue_rating'    => 'required|integer|min:1|max:5',
            'overall_rating'  => 'required|integer|min:1|max:5',
            'would_recommend' => 'required|boolean',
            'comments'        => 'nullable|string|max:1000',
        ]);

        $attendance = TrainingAttendance::where('id', $validated['attendance_id'])
            ->where('course_id', $id)
            ->firstOrFail();

        // Only an actually-marked attendance may leave feedback, and only once.
        if (!$attendance->qr_used_at) {
            return redirect()->route('attendance.success', ['id' => $id])
                ->with('error', 'Please mark your attendance before submitting feedback.');
        }

        TrainingFeedback::updateOrCreate(
            ['attendance_id' => $attendance->id],
            [
                'staff_id'        => $attendance->staff_id,
                'course_id'       => $attendance->course_id,
                'content_rating'  => $validated['content_rating'],
                'trainer_rating'  => $validated['trainer_rating'],
                'venue_rating'    => $validated['venue_rating'],
                'overall_rating'  => $validated['overall_rating'],
                'would_recommend' => $validated['would_recommend'],
                'comments'        => $validated['comments'] ?? null,
            ]
        );

        return redirect()->route('attendance.success', ['id' => $id])
            ->with('staff_name', session('staff_name', 'Staff'))
            ->with('feedback_done', true);
    }
}
