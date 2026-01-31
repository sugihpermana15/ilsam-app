<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_igi_device_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->index();

            $table->timestamp('maintenance_at')->nullable();
            $table->string('type')->nullable(); // Preventive/Corrective/Upgrade/Reimage
            $table->text('description')->nullable();
            $table->string('performed_by')->nullable();
            $table->string('attachment_path')->nullable();
            $table->date('next_schedule_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_igi_device_maintenances');
    }
};
