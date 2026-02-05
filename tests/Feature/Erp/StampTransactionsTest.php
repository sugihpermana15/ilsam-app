<?php

namespace Tests\Feature\Erp;

use App\Models\Stamp;
use App\Models\StampBalance;
use App\Models\User;
use App\Services\StampStockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StampTransactionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO sqlite driver is not available (pdo_sqlite).');
        }

        parent::setUp();
    }

    public function test_post_in_creates_transaction_and_increases_balance(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $stamp = Stamp::query()->create([
            'code' => 'MTR-10000',
            'name' => 'Materai 10.000',
            'face_value' => 10000,
            'is_active' => true,
        ]);

        /** @var StampStockService $service */
        $service = app(StampStockService::class);

        $trx = $service->postIn([
            'stamp_id' => $stamp->id,
            'qty' => 5,
        ]);

        $this->assertSame('IN', $trx->trx_type);
        $this->assertSame(5, (int) $trx->qty);
        $this->assertSame(5, (int) $trx->on_hand_after);

        $balance = StampBalance::query()->where('stamp_id', $stamp->id)->first();
        $this->assertNotNull($balance);
        $this->assertSame(5, (int) $balance->on_hand_qty);
    }

    public function test_post_out_rejects_insufficient_balance(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $stamp = Stamp::query()->create([
            'code' => 'MTR-10000',
            'name' => 'Materai 10.000',
            'face_value' => 10000,
            'is_active' => true,
        ]);

        /** @var StampStockService $service */
        $service = app(StampStockService::class);

        $service->postIn([
            'stamp_id' => $stamp->id,
            'qty' => 2,
        ]);

        $this->expectExceptionMessage('Stok materai tidak mencukupi.');

        $service->postOut([
            'stamp_id' => $stamp->id,
            'qty' => 3,
        ]);
    }
}
