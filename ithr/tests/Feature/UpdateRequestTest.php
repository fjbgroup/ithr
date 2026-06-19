<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UpdateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_my_requests_page_loads_successfully()
    {
        $user = User::factory()->create();

        // Create some requests for the user
        UpdateRequest::create([
            'requester_id' => $user->id,
            'requester_name' => $user->name,
            'record_type' => 'Staff Data',
            'record_id' => 1,
            'record_reference' => 'REF001',
            'message' => 'Test request',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($user)->get(route('my-requests', [], false));

        $response->assertStatus(200);
        $response->assertViewHas('counts');
        $response->assertViewHas('requests');
        
        $counts = $response->viewData('counts');
        $this->assertEquals(1, $counts['All']);
        $this->assertEquals(1, $counts['Pending']);
        $this->assertEquals(0, $counts['Resolved']);
        $this->assertEquals(0, $counts['Dismissed']);
    }

    public function test_user_can_submit_update_request()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('requests.store'), [
            'record_type' => 'Staff Data',
            'record_id' => 123,
            'record_reference' => 'S123',
            'fields' => ['Legal Name', 'Email'],
            'message' => 'Please update my name and email'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('update_requests', [
            'requester_id' => $user->id,
            'record_type' => 'Staff Data',
            'record_id' => 123,
            'message' => "Fields to update: Legal Name, Email\n\nPlease update my name and email"
        ]);
    }
}
