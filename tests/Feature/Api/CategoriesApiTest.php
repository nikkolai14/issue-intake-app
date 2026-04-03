<?php

namespace Tests\Feature\Api;

use App\Models\ApiKey;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories_with_valid_api_key(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        Category::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'created_at'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_cannot_access_api_without_api_key(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(401)
            ->assertJson(['message' => 'API key is required']);
    }

    public function test_can_create_category_with_valid_api_key(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->postJson('/api/categories', [
                'name' => 'Bug Report',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Category created successfully',
                'data' => [
                    'name' => 'Bug Report',
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Bug Report',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_update_category(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->putJson("/api/categories/{$category->id}", [
                'name' => 'Updated Category',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully',
                'data' => [
                    'name' => 'Updated Category',
                ],
            ]);
    }

    public function test_can_delete_category(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Category deleted successfully']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
