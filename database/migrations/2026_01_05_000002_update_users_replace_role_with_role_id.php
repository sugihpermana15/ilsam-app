<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      if (Schema::hasColumn('users', 'role')) {
        $table->dropColumn('role');
      }
      if (!Schema::hasColumn('users', 'role_id')) {
        $table->unsignedBigInteger('role_id')->default(3)->after('password');
        $table->foreign('role_id')->references('id')->on('roles');
      }
    });
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      if (Schema::hasColumn('users', 'role_id')) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
      }
      if (!Schema::hasColumn('users', 'role')) {
        $table->string('role')->nullable();
      }
    });
  }
};
