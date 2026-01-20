<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('deleted_users', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('name');
      $table->string('username')->nullable();
      $table->string('email');
      $table->string('role')->nullable();
      $table->timestamp('deleted_at');
      $table->unsignedBigInteger('deleted_by')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('deleted_users');
  }
};
