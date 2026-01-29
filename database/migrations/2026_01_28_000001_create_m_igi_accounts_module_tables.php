<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_igi_account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Enterprise Location master (plant/site + building/floor/room/rack)
        Schema::create('m_igi_locations', function (Blueprint $table) {
            $table->id();
            $table->string('plant_site', 100);
            $table->string('building', 100)->nullable();
            $table->string('floor', 50)->nullable();
            $table->string('room_rack', 100)->nullable();
            $table->string('name', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['plant_site']);
        });

        Schema::create('m_igi_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_type_id')->constrained('m_igi_account_types');
            $table->string('environment', 50)->nullable(); // prod/nonprod/internal/external

            // Prefer asset linkage, but allow null for SaaS.
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            // Snapshot fields (for audit stability even if master changes)
            $table->string('asset_code_snapshot', 100)->nullable();
            $table->string('asset_name_snapshot', 200)->nullable();
            $table->string('plant_site_snapshot', 100)->nullable();
            $table->string('location_name_snapshot', 150)->nullable();

            $table->string('department_owner', 100)->nullable();
            $table->string('criticality', 20)->nullable(); // low/medium/high

            $table->string('status', 30)->default('active'); // active/rotated/deprecated
            $table->string('vendor_installer', 150)->nullable();

            $table->timestamp('last_verified_at')->nullable();
            $table->unsignedBigInteger('last_verified_by')->nullable();

            $table->text('note')->nullable();
            $table->json('metadata')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['account_type_id', 'status']);
            $table->index(['asset_id']);
            $table->index(['plant_site_snapshot']);

            $table->foreign('asset_id')->references('id')->on('m_igi_asset');
            $table->foreign('location_id')->references('id')->on('m_igi_locations');
            $table->foreign('last_verified_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        Schema::create('m_igi_account_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('m_igi_accounts');

            $table->string('service', 50); // web/hikconnect/ssh/telnet/vpn/etc
            $table->string('protocol', 20)->nullable();
            $table->string('ip_local', 45)->nullable();
            $table->string('ip_public', 45)->nullable();
            $table->string('hostname', 255)->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->string('path', 255)->nullable();
            $table->boolean('is_management')->default(false);

            $table->json('metadata')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['account_id', 'service']);
        });

        Schema::create('m_igi_account_secrets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('m_igi_accounts');

            $table->string('label', 50)->nullable(); // admin/viewer/maintenance
            $table->string('kind', 20)->default('current'); // default/current

            $table->string('username', 150)->nullable();

            // Encrypted secret (never plaintext)
            $table->longText('secret_ciphertext');
            $table->string('secret_algo', 50)->default('laravel-crypt');
            $table->unsignedInteger('secret_key_version')->default(1);

            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('rotated_from_secret_id')->nullable();

            $table->json('metadata')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['account_id', 'kind', 'is_active']);
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('rotated_from_secret_id')->references('id')->on('m_igi_account_secrets');
        });

        Schema::create('m_igi_account_access_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();

            $table->string('action', 60);
            $table->string('result', 20)->default('success'); // success/denied/failed
            $table->string('reason', 255)->nullable();

            $table->string('target_type', 50)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();

            $table->string('ip_address', 60)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('request_id', 100)->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'action']);
            $table->index(['actor_user_id']);

            $table->foreign('actor_user_id')->references('id')->on('users');
            $table->foreign('account_id')->references('id')->on('m_igi_accounts');
        });

        Schema::create('m_igi_account_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('m_igi_accounts');
            $table->string('change_type', 50);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->json('diff')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'change_type']);
            $table->foreign('changed_by')->references('id')->on('users');
        });

        Schema::create('m_igi_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->string('request_type', 50); // reveal_secret/export_with_secrets
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('secret_id')->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('pending'); // pending/approved/rejected/expired
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'request_type']);
            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('account_id')->references('id')->on('m_igi_accounts');
            $table->foreign('secret_id')->references('id')->on('m_igi_account_secrets');
        });

        // Seed default account types (idempotent-ish for fresh install).
        foreach (['CCTV', 'Router/WiFi'] as $name) {
            DB::table('m_igi_account_types')->updateOrInsert(['name' => $name], ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('m_igi_approval_requests');
        Schema::dropIfExists('m_igi_account_changes');
        Schema::dropIfExists('m_igi_account_access_logs');
        Schema::dropIfExists('m_igi_account_secrets');
        Schema::dropIfExists('m_igi_account_endpoints');
        Schema::dropIfExists('m_igi_accounts');
        Schema::dropIfExists('m_igi_locations');
        Schema::dropIfExists('m_igi_account_types');
    }
};
