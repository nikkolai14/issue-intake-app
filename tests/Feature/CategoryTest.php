<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_categories(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->get('/categories');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('categories/index')
            ->has('categories', 3));
    }

    public function test_authenticated_user_can_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Bug',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Bug',
            'user_id' => $user->id,
        ]);
    }

    public function test_category_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_cannot_create_duplicate_category_name(): void
    {
        $user = User::factory()->create();
        Category::factory()->for($user)->create(['name' => 'Bug']);

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Bug',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_different_users_can_have_same_category_name(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Category::factory()->for($user1)->create(['name' => 'Bug']);

        $response = $this->actingAs($user2)->post('/categories', [
            'name' => 'Bug',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('categories', 2);
        $this->assertDatabaseHas('categories', [
            'name' => 'Bug',
            'user_id' => $user2->id,
        ]);
    }

    public function test_user_can_update_category_to_same_name(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['name' => 'Bug']);

        $response = $this->actingAs($user)->put("/categories/{$category->id}", [
            'name' => 'Bug',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Bug',
        ]);
    }

    public function test_user_cannot_update_category_to_duplicate_name(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->for($user)->create(['name' => 'Bug']);
        $category2 = Category::factory()->for($user)->create(['name' => 'Feature']);

        $response = $this->actingAs($user)->put("/categories/{$category2->id}", [
            'name' => 'Bug',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('categories', [
            'id' => $category2->id,
            'name' => 'Feature',
        ]);
    }

    public function test_authenticated_user_can_update_their_own_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->put("/categories/{$category->id}", [
            'name' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    public function test_authenticated_user_cannot_update_other_users_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->put("/categories/{$category->id}", [
            'name' => 'New Name',
        ]);

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_delete_their_own_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_authenticated_user_cannot_delete_other_users_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->delete("/categories/{$category->id}");

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_categories(): void
    {
        $response = $this->get('/categories');

        $response->assertRedirect('/login');
    }
}
