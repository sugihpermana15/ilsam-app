<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_igi_devices', function (Blueprint $table) {
            $table->id();

            // Link to Asset + snapshot fields (auto-filled from assets)
            $table->unsignedBigInteger('asset_id')->nullable()->index();
            $table->string('asset_code')->nullable()->index();
            $table->string('asset_name')->nullable();
            $table->string('asset_serial_number')->nullable();
            $table->string('asset_status')->nullable();
            $table->string('asset_location')->nullable();
            $table->string('asset_department')->nullable();
            $table->unsignedBigInteger('asset_department_id')->nullable();
            $table->string('asset_person_in_charge')->nullable();
            $table->unsignedBigInteger('asset_person_in_charge_employee_id')->nullable();
            $table->json('asset_payload')->nullable();

            // Identitas & OS
            $table->string('device_name')->nullable();
            $table->string('device_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('os_name')->nullable();
            $table->string('os_edition')->nullable();
            $table->string('os_version')->nullable();
            $table->string('domain_workgroup')->nullable();
            $table->string('processor')->nullable();
            $table->unsignedInteger('ram_gb')->nullable();
            $table->unsignedInteger('storage_total_gb')->nullable();
            $table->string('storage_type')->nullable();
            $table->string('gpu')->nullable();

            // Lokasi detail (opsional)
            $table->string('location_site')->nullable();
            $table->string('location_room')->nullable();

            // Kepemilikan
            $table->string('owner_type')->nullable(); // Company / Personal
            $table->string('device_role')->nullable(); // Laptop / Desktop / MiniPC / VM

            // Jaringan
            $table->string('mac_lan')->nullable();
            $table->string('mac_wifi')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('subnet_mask')->nullable();
            $table->string('gateway')->nullable();
            $table->string('dns')->nullable();
            $table->string('connectivity')->nullable(); // LAN / WiFi
            $table->string('ssid')->nullable();
            $table->decimal('internet_download_mbps', 10, 2)->nullable();
            $table->decimal('internet_upload_mbps', 10, 2)->nullable();

            // Remote Access (sensitif)
            $table->string('remote_app_type')->nullable();
            $table->string('remote_id')->nullable();
            $table->text('remote_password')->nullable();
            $table->boolean('remote_unattended')->default(false);
            $table->text('remote_notes')->nullable();

            // Credential management (sensitif)
            $table->boolean('vault_mode')->default(true);
            $table->string('local_admin_username')->nullable();
            $table->text('local_admin_password')->nullable();

            // Maintenance
            $table->timestamp('last_maintenance_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_igi_devices');
    }
};
