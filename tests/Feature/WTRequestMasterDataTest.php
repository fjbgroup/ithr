<?php

namespace Tests\Feature;

use App\Models\WT\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WTRequestMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_user_request_store_persists_typed_master_data_values(): void
    {
        $admin = User::create([
            'name' => 'Executive One',
            'username' => 'EXEC-ONE',
            'staff_no' => 'A001',
            'dept_name' => 'ICT',
            'position' => 'Executive',
            'wt_role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $requester = User::create([
            'name' => 'Requester One',
            'username' => 'REQ-ONE',
            'staff_no' => 'U001',
            'dept_name' => 'HR',
            'position' => 'Officer',
            'wt_role' => 'user',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($requester, 'wt')
            ->post(route('wt.user.requests.store', [], false), [
                'submit_to_admin_id' => $admin->id,
                'requestor_name' => 'Requester One',
                'requestor_staff_id' => 'U001',
                'request_date' => '2026-07-01',
                'requestor_dept' => ['Operations', 'Finance'],
                'position' => 'Officer',
                'ownership_type' => 'shared',
                'shared_with' => 'Team Alpha',
                'bay_from' => '3',
                'sector' => 'Engineering',
                'location' => 'Block B',
                'event_name' => 'Project Work',
                'justification' => 'Need access for work',
                'request_signature' => 'data:image/png;base64,abc',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('wt_master_data', [
            'category' => 'department',
            'value' => 'OPERATIONS',
        ]);
        $this->assertDatabaseHas('wt_master_data', [
            'category' => 'department',
            'value' => 'FINANCE',
        ]);
        $this->assertDatabaseHas('wt_master_data', [
            'category' => 'sector',
            'value' => 'ENGINEERING',
        ]);
        $this->assertDatabaseHas('wt_master_data', [
            'category' => 'location',
            'value' => 'BLOCK B',
        ]);
    }
}
