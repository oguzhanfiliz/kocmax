<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryMenuEndpointTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_only_active_categories_marked_as_in_menu_with_children(): void
    {
        // Parent categories
        $parentInMenu = Category::factory()->create([
            'name' => 'Parent In Menu',
            'is_active' => true,
            'is_in_menu' => true,
            'sort_order' => 1,
        ]);

        $parentNotInMenu = Category::factory()->create([
            'name' => 'Parent Not In Menu',
            'is_active' => true,
            'is_in_menu' => false,
            'sort_order' => 2,
        ]);

        $inactiveParentInMenu = Category::factory()->create([
            'name' => 'Inactive Parent In Menu',
            'is_active' => false,
            'is_in_menu' => true,
            'sort_order' => 3,
        ]);

        // Children for the in-menu parent
        $childInMenu = Category::factory()->create([
            'name' => 'Child In Menu',
            'parent_id' => $parentInMenu->id,
            'is_active' => true,
            'is_in_menu' => true,
            'sort_order' => 1,
        ]);

        $childNotInMenu = Category::factory()->create([
            'name' => 'Child Not In Menu',
            'parent_id' => $parentInMenu->id,
            'is_active' => true,
            'is_in_menu' => false,
            'sort_order' => 2,
        ]);

        $childInactiveInMenu = Category::factory()->create([
            'name' => 'Child Inactive In Menu',
            'parent_id' => $parentInMenu->id,
            'is_active' => false,
            'is_in_menu' => true,
            'sort_order' => 3,
        ]);

        $response = $this->getJson('/api/v1/categories/menu?with_children=1');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Menü kategorileri başarıyla getirildi',
            ]);

        $data = $response->json('data');

        // Only one parent category should be listed (active + in_menu)
        $this->assertCount(1, $data);
        $this->assertEquals($parentInMenu->id, $data[0]['id']);
        $this->assertTrue($data[0]['is_in_menu']);

        // Children: only active + in_menu child should be present
        $children = $data[0]['children'];
        $this->assertCount(1, $children);
        $this->assertEquals($childInMenu->id, $children[0]['id']);
        $this->assertTrue($children[0]['is_in_menu']);
    }

    /** @test */
    public function it_can_include_non_root_categories_in_flat_list_when_requested(): void
    {
        $rootA = Category::factory()->create([
            'name' => 'Root A',
            'is_active' => true,
            'is_in_menu' => true,
            'sort_order' => 1,
        ]);

        $childA1 = Category::factory()->create([
            'name' => 'Child A1',
            'parent_id' => $rootA->id,
            'is_active' => true,
            'is_in_menu' => true,
            'sort_order' => 1,
        ]);

        $childA2NotInMenu = Category::factory()->create([
            'name' => 'Child A2 Not In Menu',
            'parent_id' => $rootA->id,
            'is_active' => true,
            'is_in_menu' => false,
            'sort_order' => 2,
        ]);

        $rootBNotInMenu = Category::factory()->create([
            'name' => 'Root B Not In Menu',
            'is_active' => true,
            'is_in_menu' => false,
            'sort_order' => 2,
        ]);

        $response = $this->getJson('/api/v1/categories/menu?include_non_root=1');

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id');

        // Root A ve Child A1 dahil olmalı; Root B Not In Menu ve Child A2 Not In Menu dahil olmamalı
        $this->assertTrue($ids->contains($rootA->id));
        $this->assertTrue($ids->contains($childA1->id));
        $this->assertFalse($ids->contains($rootBNotInMenu->id));
        $this->assertFalse($ids->contains($childA2NotInMenu->id));
    }
}


