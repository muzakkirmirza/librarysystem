<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ItemCategory;

class InventoryItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_inventory_items()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/inventory-items');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_inventory_item()
    {
        $user = User::factory()->create();
        $category = ItemCategory::create([
            'name' => 'Electronics',
            'description' => 'Lab Hardware'
        ]);

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/inventory-items', [
                             'category_id' => $category->id,
                             'name' => 'Dell Latitude Laptop',
                             'condition' => 'Good',
                             'is_available' => true,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Dell Latitude Laptop');
    }
}
