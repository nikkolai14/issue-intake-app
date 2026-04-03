<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_generate_api_key(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('api-key.store'), [
                'name' => 'Test API Key',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('api_keys', [
            'user_id' => $user->id,
            'name' => 'Test API Key',
        ]);
    }

    public function test_user_can_delete_api_key(): void
    {
        $user = User::factory()->create();

        // Generate an API key
        $this->actingAs($user)->post(route('api-key.store'));

        // Refresh to get the relationship
        $user->refresh();

        $this->assertDatabaseCount('api_keys', 1);

        $response = $this->actingAs($user)
            ->delete(route('api-key.destroy'));

        $response->assertRedirect();
        $this->assertDatabaseCount('api_keys', 0);
    }

    public function test_generating_new_key_replaces_existing_one(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('api-key.store'), ['name' => 'First Key']);
        $user->refresh();

        $this->assertDatabaseCount('api_keys', 1);

        $this->actingAs($user)->post(route('api-key.store'), ['name' => 'Second Key']);

        $this->assertDatabaseCount('api_keys', 1);
        $this->assertDatabaseHas('api_keys', [
            'user_id' => $user->id,
            'name' => 'Second Key',
        ]);
        $this->assertDatabaseMissing('api_keys', [
            'user_id' => $user->id,
            'name' => 'First Key',
        ]);
    }
}
