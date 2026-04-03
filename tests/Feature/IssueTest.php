<?php

namespace Tests\Feature;

use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Category;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_index_page_displays_issues(): void
    {
        $user = User::factory()->create();
        $issues = Issue::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->get('/issues');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('issues')
            ->has('issues', 3));
    }

    public function test_authenticated_user_can_create_issue(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/issues', [
            'title' => 'Test Issue',
            'description' => 'This is a test issue description.',
            'status' => Status::Todo->value,
            'priority' => Priority::High->value,
        ]);

        $response->assertRedirect('/issues');
        $this->assertDatabaseHas('issues', [
            'title' => 'Test Issue',
            'description' => 'This is a test issue description.',
            'status' => Status::Todo->value,
            'priority' => Priority::High->value,
            'user_id' => $user->id,
        ]);
    }

    public function test_issue_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/issues', [
            'title' => '',
            'description' => 'Test description',
            'status' => Status::Todo->value,
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_issue_description_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/issues', [
            'title' => 'Test Issue',
            'description' => '',
            'status' => Status::Todo->value,
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_issue_status_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/issues', [
            'title' => 'Test Issue',
            'description' => 'Test description',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_priority_field_is_optional(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/issues', [
            'title' => 'Test Issue',
            'description' => 'Test description',
            'status' => Status::Todo->value,
        ]);

        $response->assertRedirect('/issues');
        $this->assertDatabaseHas('issues', [
            'title' => 'Test Issue',
            'priority' => null,
        ]);
    }

    public function test_user_cannot_create_issue_with_duplicate_title(): void
    {
        $user = User::factory()->create();
        Issue::factory()->for($user)->create(['title' => 'Duplicate Title']);

        $response = $this->actingAs($user)->post('/issues', [
            'title' => 'Duplicate Title',
            'description' => 'Test description',
            'status' => Status::Todo->value,
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_different_users_can_have_issues_with_same_title(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Issue::factory()->for($user1)->create(['title' => 'Same Title']);

        $response = $this->actingAs($user2)->post('/issues', [
            'title' => 'Same Title',
            'description' => 'Test description',
            'status' => Status::Todo->value,
        ]);

        $response->assertRedirect('/issues');
        $this->assertEquals(2, Issue::where('title', 'Same Title')->count());
    }

    public function test_authenticated_user_can_update_their_own_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create();

        $response = $this->actingAs($user)->put("/issues/{$issue->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'status' => Status::InProgress->value,
            'priority' => Priority::Urgent->value,
        ]);

        $response->assertRedirect('/issues');
        $this->assertDatabaseHas('issues', [
            'id' => $issue->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'status' => Status::InProgress->value,
            'priority' => Priority::Urgent->value,
        ]);
    }

    public function test_user_cannot_update_issue_to_duplicate_title(): void
    {
        $user = User::factory()->create();
        $issue1 = Issue::factory()->for($user)->create(['title' => 'Existing Title']);
        $issue2 = Issue::factory()->for($user)->create(['title' => 'Original Title']);

        $response = $this->actingAs($user)->put("/issues/{$issue2->id}", [
            'title' => 'Existing Title',
            'description' => 'Updated description',
            'status' => Status::InProgress->value,
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_user_can_update_issue_keeping_same_title(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create(['title' => 'Original Title']);

        $response = $this->actingAs($user)->put("/issues/{$issue->id}", [
            'title' => 'Original Title',
            'description' => 'Updated description',
            'status' => Status::InProgress->value,
        ]);

        $response->assertRedirect('/issues');
        $this->assertDatabaseHas('issues', [
            'id' => $issue->id,
            'title' => 'Original Title',
            'description' => 'Updated description',
        ]);
    }

    public function test_user_can_update_issue_to_title_used_by_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Issue::factory()->for($user1)->create(['title' => 'Shared Title']);
        $issue2 = Issue::factory()->for($user2)->create(['title' => 'Original Title']);

        $response = $this->actingAs($user2)->put("/issues/{$issue2->id}", [
            'title' => 'Shared Title',
            'description' => 'Updated description',
            'status' => Status::InProgress->value,
        ]);

        $response->assertRedirect('/issues');
        $this->assertDatabaseHas('issues', [
            'id' => $issue2->id,
            'title' => 'Shared Title',
            'user_id' => $user2->id,
        ]);
    }

    public function test_authenticated_user_cannot_update_other_users_issue(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $issue = Issue::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->put("/issues/{$issue->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'status' => Status::InProgress->value,
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_delete_their_own_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/issues/{$issue->id}");

        $response->assertRedirect('/issues');
        $this->assertDatabaseMissing('issues', ['id' => $issue->id]);
    }

    public function test_authenticated_user_cannot_delete_other_users_issue(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $issue = Issue::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->delete("/issues/{$issue->id}");

        $response->assertForbidden();
    }

    public function test_issue_can_be_attached_to_multiple_categories(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->post('/issues', [
            'title' => 'Test Issue',
            'description' => 'Test description',
            'status' => Status::Todo->value,
            'category_ids' => $categories->pluck('id')->toArray(),
        ]);

        $response->assertRedirect('/issues');

        $issue = Issue::where('title', 'Test Issue')->first();
        $this->assertCount(3, $issue->categories);
    }

    public function test_guest_cannot_access_issues(): void
    {
        $response = $this->get('/issues');

        $response->assertRedirect('/login');
    }

    public function test_edit_endpoint_returns_json_for_ajax_requests(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(2)->for($user)->create();
        $issue = Issue::factory()->for($user)->create([
            'priority' => Priority::High->value,
            'status' => Status::InProgress->value,
        ]);
        $issue->categories()->attach($categories->pluck('id'));

        $response = $this->actingAs($user)
            ->getJson("/issues/{$issue->id}/edit");

        $response->assertOk();
        $response->assertJson([
            'issue' => [
                'id' => $issue->id,
                'title' => $issue->title,
                'description' => $issue->description,
                'priority' => Priority::High->value,
                'status' => Status::InProgress->value,
                'category_ids' => $categories->pluck('id')->toArray(),
            ],
        ]);
        $response->assertJsonStructure([
            'issue',
            'categories',
            'priorities',
            'statuses',
        ]);
    }
}
