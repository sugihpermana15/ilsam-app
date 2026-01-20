<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('m_igi_transfer_asset', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('asset_id');
      $table->string('asset_code');
      $table->string('asset_name');
      $table->string('asset_category')->nullable();
      $table->string('asset_location')->nullable();
      $table->string('person_in_charge')->nullable();
      $table->string('asset_status')->nullable();
      $table->date('purchase_date')->nullable();
      $table->decimal('price', 18, 2)->nullable();
      $table->string('asset_condition')->nullable();
      $table->string('ownership_status')->nullable();
      $table->text('description')->nullable();
      $table->timestamp('last_updated')->nullable();
      $table->timestamp('transferred_at')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('m_igi_transfer_asset');
  }
};
