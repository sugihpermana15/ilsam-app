<?php

namespace Tests\Feature;

use App\Http\Middleware\MenuPermissionMiddleware;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetCategoryCodeUpdateTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
      $this->markTestSkipped('PDO SQLite driver is not available in this PHP runtime.');
    }

    parent::setUp();
  }

  public function test_changing_category_regenerates_asset_code_using_destination_category_sequence(): void
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

    AssetCategory::query()->create([
      'code' => 'DSP',
      'name' => 'Display',
      'asset_code_prefix' => 'DSP',
      'is_active' => true,
    ]);
    AssetCategory::query()->create([
      'code' => 'PER',
      'name' => 'Peripheral',
      'asset_code_prefix' => 'PER',
      'is_active' => true,
    ]);
    AssetLocation::query()->create([
      'name' => 'Karawang',
      'asset_code_prefix' => '02',
      'is_active' => true,
    ]);

    $asset = Asset::query()->create([
      'asset_code' => 'IGI-DSP-02-20260819-000013',
      'asset_name' => 'Keyboard',
      'asset_category' => 'DSP',
      'asset_location' => 'Karawang',
      'start_use_date' => '2026-08-19',
      'asset_status' => 'Active',
    ]);
    Asset::query()->create([
      'asset_code' => 'IGI-PER-02-20260819-000037',
      'asset_name' => 'Existing peripheral',
      'asset_category' => 'PER',
      'asset_location' => 'Karawang',
    ]);

    $this->withoutMiddleware(MenuPermissionMiddleware::class)
      ->actingAs($user)
      ->put(route('admin.assets.update', $asset->id), [
        'asset_name' => 'Keyboard',
        'asset_category' => 'PER',
        'asset_location' => 'Karawang',
        'start_use_date' => '2026-08-19',
        'asset_status' => 'Active',
      ])
      ->assertRedirect(route('admin.assets.index'));

    $asset->refresh();

    $this->assertSame('PER', $asset->asset_category);
    $this->assertSame('IGI-PER-02-20260819-000038', $asset->asset_code);
    $this->assertDatabaseMissing('m_igi_asset', [
      'id' => $asset->id,
      'asset_code' => 'IGI-DSP-02-20260819-000013',
    ]);
  }
}
