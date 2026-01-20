<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('deleted_asset', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('asset_id');
      $table->string('asset_code');
      $table->string('asset_name');
      $table->string('asset_category');
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
      $table->timestamp('input_date')->nullable();
      $table->string('input_by')->nullable();
      $table->timestamp('last_updated')->nullable();
      $table->text('notes')->nullable();
      $table->string('image_1')->nullable();
      $table->string('image_2')->nullable();
      $table->string('image_3')->nullable();
      $table->timestamp('deleted_at');
      $table->unsignedBigInteger('deleted_by')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('deleted_asset');
  }
};
