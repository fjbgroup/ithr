<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\TrainingAttendance;
use App\Models\TrainingCourse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QrAttendanceLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    private function makeContext(): array
    {
        $user = User::factory()->create([
            'staff_no'  => 'S1001',
            'password'  => Hash::make('secret123'),
            'is_active' => 1,
            'name'      => 'Jane Doe',
        ]);

        $staff = Staff::create([
            'staff_no'  => 'S1001',
            'name'      => 'Jane Doe',
            'is_active' => 1,
        ]);

        $course = TrainingCourse::create([
            'code'          => 'FS-001',
            'title'         => 'Fire Safety',
            'training_type' => 'Internal',
        ]);

        $token = 'tok_' . $course->id;
        Cache::put("training_token_{$course->id}", $token, 40);

        return [$user, $staff, $course, $token];
    }

    public function test_qr_submit_logs_the_staff_in(): void
    {
        [$user, $staff, $course, $token] = $this->makeContext();

        $this->assertGuest();

        $response = $this->post(
            route('attendance.verify.submit', ['id' => $course->id]) . "?token={$token}",
            ['staff_no' => 'S1001', 'password' => 'secret123']
        );

        // Marked + redirected to the success page...
        $response->assertRedirect(route('attendance.success', ['id' => $course->id]));
        $this->assertDatabaseHas('training_attendances', [
            'staff_id'  => $staff->id,
            'course_id' => $course->id,
            'status'    => 'Completed',
        ]);

        // ...and crucially, the session is now authenticated, so "View My
        // Training" (an auth-protected route) no longer bounces to login.
        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_does_not_log_in(): void
    {
        [$user, $staff, $course, $token] = $this->makeContext();

        $response = $this->post(
            route('attendance.verify.submit', ['id' => $course->id]) . "?token={$token}",
            ['staff_no' => 'S1001', 'password' => 'WRONG']
        );

        $response->assertSessionHasErrors('staff_no');
        $this->assertGuest();
        $this->assertDatabaseMissing('training_attendances', [
            'course_id' => $course->id,
        ]);
    }
}
