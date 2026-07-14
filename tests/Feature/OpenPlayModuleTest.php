<?php

namespace Tests\Feature;

use App\Models\OpenPlayAccessRequest;
use App\Models\OpenPlayState;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class OpenPlayModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_always_has_open_play_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/open-play')
            ->assertOk()
            ->assertSee('PB Queue');
    }

    public function test_customer_can_request_open_play_access(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/open-play/access-request', [
            'message' => 'We run weekly community games.',
        ])->assertRedirect();

        $this->assertDatabaseHas('open_play_access_requests', [
            'user_id' => $customer->id,
            'status' => 'pending',
        ]);
    }

    public function test_approved_customer_state_is_saved_in_the_database(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        OpenPlayAccessRequest::query()->create(['user_id' => $customer->id, 'status' => 'approved']);

        $this->actingAs($customer)->putJson('/open-play/state', [
            'state' => ['screen' => 'home', 'history' => []],
        ])->assertOk()->assertJson(['saved' => true]);

        $this->assertSame('home', OpenPlayState::query()->firstOrFail()->state['screen']);
    }
}
