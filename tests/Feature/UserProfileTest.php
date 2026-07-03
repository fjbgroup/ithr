<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Staff;
use App\Models\FamilyMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_user_can_update_own_profile(): void
    {
        $staff = Staff::create([
            'staff_no' => 'S1001',
            'name' => 'Jane Doe',
            'is_active' => 1,
            'email' => 'jane@example.com',
        ]);

        $user = User::factory()->create([
            'staff_no' => 'S1001',
            'staff_id' => $staff->id,
            'is_active' => 1,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'role' => 'staff',
        ]);

        $response = $this->actingAs($user)->post('/profile/update', [
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'phone_number' => '1234567890',
            'gender' => 'Female',
            'date_of_birth' => '1995-05-15',
            'ic_number' => 'IC950515',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
        ]);

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'phone_number' => '1234567890',
            'gender' => 'Female',
            'date_of_birth' => '1995-05-15',
            'ic_number' => 'IC950515',
        ]);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $staff = Staff::create([
            'staff_no' => 'S1001',
            'name' => 'Jane Doe',
            'is_active' => 1,
        ]);

        $user = User::factory()->create([
            'staff_no' => 'S1001',
            'staff_id' => $staff->id,
            'is_active' => 1,
            'name' => 'Jane Doe',
            'role' => 'staff',
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->post('/profile/update', [
            'name' => 'Jane Doe',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_family_member_crud_via_controllers(): void
    {
        $staff = Staff::create([
            'staff_no' => 'S1001',
            'name' => 'Jane Doe',
            'is_active' => 1,
        ]);

        $user = User::factory()->create([
            'staff_no' => 'S1001',
            'staff_id' => $staff->id,
            'is_active' => 1,
            'name' => 'Jane Doe',
            'role' => 'staff',
        ]);

        // 1. Create family member
        $response = $this->actingAs($user)->post('/family', [
            'name' => 'John Doe',
            'relationship' => 'Spouse',
            'date_of_birth' => '1990-01-01',
            'emergency_contact' => 'Yes',
            'phone_number' => '012345678',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('family_members', [
            'staff_id' => $staff->id,
            'family_member_name' => 'John Doe',
            'relationship' => 'Spouse',
        ]);

        $familyMember = FamilyMember::where('family_member_name', 'John Doe')->first();

        // 2. Update family member
        $response = $this->actingAs($user)->put("/family/{$familyMember->id}", [
            'name' => 'John Doe updated',
            'relationship' => 'Spouse',
            'date_of_birth' => '1990-01-01',
            'emergency_contact' => 'Yes',
            'phone_number' => '098765432',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('family_members', [
            'id' => $familyMember->id,
            'family_member_name' => 'John Doe updated',
            'phone_number' => '098765432',
        ]);

        // 3. Delete family member
        $response = $this->actingAs($user)->delete("/family/{$familyMember->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('family_members', [
            'id' => $familyMember->id,
        ]);
    }
}
