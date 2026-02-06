<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'stamp_validator_user_id')) {
                return;
            }

            $table->unsignedBigInteger('stamp_validator_user_id')->nullable()->after('employee_id');
            $table->index('stamp_validator_user_id', 'users_stamp_validator_user_id_index');
            $table->foreign('stamp_validator_user_id', 'users_stamp_validator_user_id_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'stamp_validator_user_id')) {
                return;
            }

            $table->dropForeign('users_stamp_validator_user_id_foreign');
            $table->dropIndex('users_stamp_validator_user_id_index');
            $table->dropColumn('stamp_validator_user_id');
        });
    }
};
