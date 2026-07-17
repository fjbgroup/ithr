<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\IT\User as ItUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ITUserManualTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_it_user_manual(): void
    {
        $response = $this->get('/it/user-manual');
        $response->assertRedirect('/it/login');
    }

    public function test_staff_can_only_access_it_staff_manual(): void
    {
        $staff = ItUser::find(User::factory()->create([
            'it_role' => 'user',
            'is_active' => true,
        ])->id);

        $response = $this->actingAs($staff, 'it')->get('/it/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff Manual');
        $response->assertSee('Welcome to the Staff user manual for the IT system.');
        
        $response->assertDontSee('Admin (IT) Manual');
        $response->assertDontSee('Finance Admin Manual');
        $response->assertDontSee('CEO Manual');
        $response->assertDontSee('GM Manual');
        $response->assertDontSee('Head of Unit Manual');
    }

    public function test_hou_can_access_hou_and_staff_manuals(): void
    {
        $hou = ItUser::find(User::factory()->create([
            'it_role' => 'hou',
            'is_active' => true,
        ])->id);

        $response = $this->actingAs($hou, 'it')->get('/it/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff Manual');
        $response->assertSee('Head of Unit Manual');
        $response->assertSee('Welcome to the Head of Unit user manual for the IT system.');
        
        $response->assertDontSee('Admin (IT) Manual');
        $response->assertDontSee('Finance Admin Manual');
    }

    public function test_ceo_can_access_ceo_and_staff_manuals(): void
    {
        $ceo = ItUser::find(User::factory()->create([
            'it_role' => 'ceo',
            'is_active' => true,
        ])->id);

        $response = $this->actingAs($ceo, 'it')->get('/it/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff Manual');
        $response->assertSee('CEO Manual');
        $response->assertSee('Welcome to the CEO user manual for the IT system.');
        
        $response->assertDontSee('Admin (IT) Manual');
    }

    public function test_admin_it_can_access_all_it_manuals(): void
    {
        $admin = ItUser::find(User::factory()->create([
            'it_role' => 'admin_it',
            'is_active' => true,
        ])->id);

        $response = $this->actingAs($admin, 'it')->get('/it/user-manual');

        $response->assertStatus(200);
        $response->assertSee('Staff Manual');
        $response->assertSee('Admin (IT) Manual');
        $response->assertSee('Finance Admin Manual');
        $response->assertSee('CEO Manual');
        $response->assertSee('GM Manual');
        $response->assertSee('Head of Unit Manual');
    }
}
