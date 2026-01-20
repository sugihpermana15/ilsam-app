<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_asset', function (Blueprint $table) {
      $table->id();
      $table->string('asset_code')->unique();
      $table->string('asset_name');
      $table->string('asset_category'); // IT, Vehicle, Machine, Furniture, etc
      $table->string('brand_type_model')->nullable();
      $table->string('serial_number')->nullable();
      $table->text('description')->nullable();
      $table->date('purchase_date')->nullable();
      $table->decimal('price', 18, 2)->nullable();
      $table->string('vendor_supplier')->nullable();
      $table->string('invoice_number')->nullable();
      $table->string('asset_location')->nullable();
      $table->string('department')->nullable();
      $table->string('person_in_charge')->nullable();
      $table->enum('ownership_status', ['Owned', 'Rented', 'Leased'])->nullable();
      $table->enum('asset_condition', ['Good', 'Minor Damage', 'Major Damage'])->nullable();
      $table->enum('asset_status', ['Active', 'Inactive', 'Sold', 'Disposed'])->nullable();
      $table->date('start_use_date')->nullable();
      $table->enum('warranty_status', ['Yes', 'No'])->nullable();
      $table->date('warranty_end_date')->nullable();
      $table->timestamp('input_date')->useCurrent();
      $table->string('input_by')->nullable();
      $table->timestamp('last_updated')->nullable();
      $table->text('notes')->nullable();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_asset');
  }
};
