<?php

namespace Tests\Feature\Api;

use App\Enums\Priority;
use App\Enums\Status;
use App\Models\ApiKey;
use App\Models\Category;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssuesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_issues_with_valid_api_key(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        Issue::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->getJson('/api/issues');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'priority', 'status', 'categories', 'created_at'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_issues_by_status(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        Issue::factory()->create(['user_id' => $user->id, 'status' => Status::Todo]);
        Issue::factory()->create(['user_id' => $user->id, 'status' => Status::Completed]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->getJson('/api/issues?status=todo');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_create_issue_with_categories(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->postJson('/api/issues', [
                'title' => 'Test Issue',
                'description' => 'Test Description',
                'status' => Status::Todo->value,
                'priority' => Priority::High->value,
                'category_ids' => [$category->id],
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Issue created successfully',
                'data' => [
                    'title' => 'Test Issue',
                ],
            ]);

        $this->assertDatabaseHas('issues', [
            'title' => 'Test Issue',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_update_issue(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        $issue = Issue::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->putJson("/api/issues/{$issue->id}", [
                'title' => 'Updated Issue',
                'description' => 'Updated Description',
                'status' => Status::InProgress->value,
                'priority' => Priority::Urgent->value,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Issue updated successfully',
            ]);
    }

    public function test_can_delete_issue(): void
    {
        $user = User::factory()->create();
        $apiKey = ApiKey::factory()->create(['user_id' => $user->id]);
        $issue = Issue::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->deleteJson("/api/issues/{$issue->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Issue deleted successfully']);

        $this->assertDatabaseMissing('issues', ['id' => $issue->id]);
    }
}
