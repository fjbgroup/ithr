<?php

namespace Tests\Feature;

use App\Models\IT\EwasteItem;
use App\Models\IT\User as ItUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriteoffInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_inventory_shows_all_unrouted_ceo_approved_writeoffs(): void
    {
        $financeAdmin = ItUser::find(User::factory()->create([
            'it_role' => 'finance_admin',
            'is_active' => true,
        ])->id);

        foreach ([null, '', 'Pending', 'Approved'] as $index => $financeStatus) {
            EwasteItem::create([
                'asset_number' => 'FIN-PENDING-' . $index,
                'description' => 'Awaiting Finance ' . $index,
                'ceo_status' => 'Approved',
                'finance_status' => $financeStatus,
                'disposal_status' => 'Approved',
            ]);
        }

        EwasteItem::create([
            'asset_number' => 'FIN-PROCESSED',
            'description' => 'Already routed',
            'ceo_status' => 'Approved',
            'finance_status' => 'EWaste',
            'disposal_status' => 'Approved',
        ]);

        $response = $this->actingAs($financeAdmin, 'it')->get('/it/writeoff-inventory');

        $response->assertOk();
        $response->assertViewHas('pendingCount', 4);
        $response->assertViewHas('pendingGroups', fn ($groups) => $groups->flatten()->count() === 4);
    }

    public function test_ceo_approval_hands_form_to_finance_queue_and_notifies_finance_admin(): void
    {
        $ceo = ItUser::find(User::factory()->create([
            'it_role' => 'ceo',
            'is_active' => true,
        ])->id);
        $financeAdmin = ItUser::find(User::factory()->create([
            'it_role' => 'finance_admin',
            'is_active' => true,
        ])->id);
        $item = EwasteItem::create([
            'asset_number' => 'CEO-FIN-001',
            'description' => 'CEO approved asset',
            'disposal_status' => 'Pending',
            'hou_status' => 'Checked',
            'gm_status' => 'Checked',
            'ceo_status' => 'Pending',
        ]);

        $this->actingAs($ceo, 'it')->post('/it/writeoff/ceo-approve', [
            'ceo_sign_id' => (string) $item->id,
            'ceo_action' => 'approve',
            'ceo_sig_img' => 'data:image/png;base64,test',
        ])->assertRedirect('/it/writeoff');

        $this->assertDatabaseHas('ewaste_items', [
            'id' => $item->id,
            'ceo_status' => 'Approved',
            'finance_status' => 'Pending',
        ]);
        $this->assertDatabaseHas('it_notifications', [
            'user_id' => $financeAdmin->id,
            'title' => 'Write-Off Awaiting Finance Routing',
        ]);

        $this->actingAs($financeAdmin, 'it')
            ->get('/it/writeoff-inventory')
            ->assertOk()
            ->assertViewHas('pendingGroups', fn ($groups) => $groups->flatten()->contains('id', $item->id));
    }
}
