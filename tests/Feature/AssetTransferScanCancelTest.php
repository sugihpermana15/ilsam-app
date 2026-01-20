<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Role;
use App\Models\TransferAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetTransferScanCancelTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    // This repo's phpunit config typically uses sqlite for tests.
    // If the PHP runtime lacks the sqlite PDO driver, skip to avoid false failures.
    if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
      $this->markTestSkipped('PDO SQLite driver is not available in this PHP runtime.');
    }

    parent::setUp();
  }

  private function createSuperAdminUser(): User
  {
    // Ensure role exists (some parts of the app rely on role_name)
    $role = Role::query()->create([
      'id' => 1,
      'role_name' => 'Super Admin',
    ]);

    return User::query()->create([
      'name' => 'Root',
      'username' => 'root',
      'email' => 'root@example.com',
      'password' => bcrypt('password'),
      'role_id' => $role->id,
    ]);
  }

  private function createAsset(string $code = 'IGI-IT-01-20250101-000001', string $location = 'Jababeka'): Asset
  {
    return Asset::query()->create([
      'asset_code' => $code,
      'asset_name' => 'Laptop',
      'asset_category' => 'IT',
      'asset_location' => $location,
      'asset_status' => 'Active',
    ]);
  }

  private function createOutboundTransfer(Asset $asset): TransferAsset
  {
    return TransferAsset::query()->create([
      'asset_id' => $asset->id,
      'asset_code' => $asset->asset_code,
      'asset_name' => $asset->asset_name,
      'asset_category' => $asset->asset_category,
      'asset_location' => $asset->asset_location,
      'from_location' => 'Jababeka',
      'to_location' => 'Karawang',
      'asset_status' => $asset->asset_status,
      'status' => 'OUT_REQUESTED',
      'requested_by' => 1,
      'requested_at' => now(),
      'transferred_at' => now(),
    ]);
  }

  public function test_scan_success_marks_received_and_hides_from_out_list_and_blocks_cancel(): void
  {
    $user = $this->createSuperAdminUser();
    $asset = $this->createAsset();
    $transfer = $this->createOutboundTransfer($asset);

    $this->actingAs($user)
      ->post('/admin/assets/in/scan', ['asset_code' => $asset->asset_code])
      ->assertRedirect('/admin/assets/in');

    $this->assertDatabaseHas('m_igi_transfer_asset', [
      'id' => $transfer->id,
      'status' => 'RECEIVED',
    ]);

    // Outbound list should no longer show this transfer
    $this->actingAs($user)
      ->get('/admin/assets/transfer')
      ->assertOk()
      ->assertDontSee($asset->asset_code);

    // Cancel should be rejected after received
    $this->actingAs($user)
      ->post('/admin/assets/transfer/cancel', ['selected_transfer_ids' => (string) $transfer->id])
      ->assertStatus(409);

    $this->assertDatabaseHas('m_igi_transfer_asset', [
      'id' => $transfer->id,
      'status' => 'RECEIVED',
    ]);
  }

  public function test_scan_twice_is_idempotent(): void
  {
    $user = $this->createSuperAdminUser();
    $asset = $this->createAsset();
    $transfer = $this->createOutboundTransfer($asset);

    $this->actingAs($user)
      ->post('/admin/assets/in/scan', ['asset_code' => $asset->asset_code])
      ->assertRedirect('/admin/assets/in');

    $this->actingAs($user)
      ->post('/admin/assets/in/scan', ['asset_code' => $asset->asset_code])
      ->assertRedirect('/admin/assets/in');

    $this->assertDatabaseHas('m_igi_transfer_asset', [
      'id' => $transfer->id,
      'status' => 'RECEIVED',
    ]);
  }

  public function test_cancel_before_scan_succeeds(): void
  {
    $user = $this->createSuperAdminUser();
    $asset = $this->createAsset();
    $transfer = $this->createOutboundTransfer($asset);

    $this->actingAs($user)
      ->post('/admin/assets/transfer/cancel', ['selected_transfer_ids' => (string) $transfer->id])
      ->assertRedirect('/admin/assets/transfer');

    $this->assertDatabaseHas('m_igi_transfer_asset', [
      'id' => $transfer->id,
      'status' => 'CANCELLED',
    ]);

    // Scan after cancel should be rejected
    $this->actingAs($user)
      ->post('/admin/assets/in/scan', ['asset_code' => $asset->asset_code])
      ->assertStatus(409);

    $this->assertDatabaseHas('m_igi_transfer_asset', [
      'id' => $transfer->id,
      'status' => 'CANCELLED',
    ]);
  }
}
