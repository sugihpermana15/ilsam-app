<?php

namespace Tests\Feature;

use App\Http\Middleware\MenuPermissionMiddleware;
use App\Models\Role;
use App\Models\StockItem;
use App\Models\StockItemSiteBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRequestOrderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO SQLite driver is not available in this PHP runtime.');
        }

        parent::setUp();
    }

    public function test_out_of_stock_items_are_last_and_return_to_name_order_when_restocked(): void
    {
        $role = Role::query()->create([
            'id' => 1,
            'role_name' => 'Super Admin',
        ]);
        $user = User::query()->create([
            'name' => 'Root',
            'username' => 'root',
            'email' => 'root@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $items = collect([
            ['name' => 'Beta item', 'stock' => 0],
            ['name' => 'Alpha item', 'stock' => 4],
            ['name' => 'Charlie item', 'stock' => 2],
        ])->map(function (array $data): StockItem {
            $item = StockItem::query()->create([
                'category' => 'atk',
                'code' => strtoupper(substr($data['name'], 0, 1)) . '001',
                'name' => $data['name'],
                'unit' => 'pcs',
                'unit_price' => 1000,
                'low_stock_threshold' => 5,
                'is_active' => true,
            ]);

            StockItemSiteBalance::query()->create([
                'stock_item_id' => $item->id,
                'site_code' => 'karawang',
                'on_hand_qty' => $data['stock'],
            ]);

            return $item;
        });

        $url = route('admin.stock.request.index', ['category' => 'atk', 'site' => 'karawang']);
        $response = $this->withoutMiddleware(MenuPermissionMiddleware::class)
            ->actingAs($user)
            ->get($url);

        $response->assertOk()
            ->assertSee('product-card is-out-of-stock')
            ->assertSee('Habis');
        $this->assertItemOrder($response->getContent(), [
            $items[1]->id,
            $items[2]->id,
            $items[0]->id,
        ]);

        StockItemSiteBalance::query()
            ->where('stock_item_id', $items[0]->id)
            ->where('site_code', 'karawang')
            ->update(['on_hand_qty' => 3]);

        $restockedResponse = $this->get($url);

        $restockedResponse->assertOk()
            ->assertDontSee('product-card is-out-of-stock')
            ->assertDontSee('Habis');
        $this->assertItemOrder($restockedResponse->getContent(), [
            $items[1]->id,
            $items[0]->id,
            $items[2]->id,
        ]);
    }

    private function assertItemOrder(string $html, array $itemIds): void
    {
        $positions = array_map(
            fn (int $itemId): int|false => strpos($html, 'data-item-id="' . $itemId . '"'),
            $itemIds,
        );

        foreach ($positions as $position) {
            $this->assertNotFalse($position);
        }

        for ($index = 1; $index < count($positions); $index++) {
            $this->assertTrue($positions[$index - 1] < $positions[$index]);
        }
    }
}
