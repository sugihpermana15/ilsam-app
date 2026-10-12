<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StockNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO SQLite driver is not available in this PHP runtime.');
        }

        parent::setUp();
    }

    public function test_notifications_endpoint_returns_all_notifications_and_unread_count(): void
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

        for ($number = 1; $number <= 55; $number++) {
            StockNotification::query()->create([
                'user_id' => $user->id,
                'type' => 'low_stock',
                'title' => 'Notification ' . $number,
                'message' => 'Stock is low.',
                'data' => ['category' => 'atk'],
            ]);
        }

        $this->actingAs($user)
            ->get(route('admin.stock.notifications'))
            ->assertOk()
            ->assertJsonPath('unread', 55)
            ->assertJsonCount(55, 'data');
    }
}
