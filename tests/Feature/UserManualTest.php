<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManualTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest users should be redirected to login.
     */
    public function test_guest_cannot_access_user_manual(): void
    {
        $response = $this->get('/user-manual');
        $response->assertRedirect('/login');
    }

    /**
     * Staff users should only see the Staff User Manual.
     */
    public function test_staff_can_only_access_staff_manual(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->get('/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff User Manual');
        $response->assertSee('Welcome to the HR Admin System. As a <strong>Staff</strong> member', false);
        
        $response->assertDontSee('Admin (HR) Manual');
        $response->assertDontSee('Admin (IT) Manual');
    }

    /**
     * Admin HR users should see both Staff and Admin HR manuals, but not Admin IT.
     */
    public function test_admin_hr_can_access_staff_and_hr_manuals(): void
    {
        $adminHr = User::factory()->create([
            'role' => 'admin_hr',
            'is_active' => true,
        ]);

        $response = $this->actingAs($adminHr)->get('/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff Manual');
        $response->assertSee('Admin (HR) Manual');
        $response->assertSee('As an <strong>Admin (HR)</strong> user, you are responsible for', false);
        
        $response->assertDontSee('Admin (IT) Manual');
    }

    /**
     * Admin IT users should see all manuals.
     */
    public function test_admin_it_can_access_all_manuals(): void
    {
        $adminIt = User::factory()->create([
            'role' => 'admin_it',
            'is_active' => true,
        ]);

        $response = $this->actingAs($adminIt)->get('/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff Manual');
        $response->assertSee('Admin (HR) Manual');
        $response->assertSee('Admin (IT) Manual');
        $response->assertSee('As an <strong>Admin (IT)</strong>, you have full administrative rights', false);
    }
}
