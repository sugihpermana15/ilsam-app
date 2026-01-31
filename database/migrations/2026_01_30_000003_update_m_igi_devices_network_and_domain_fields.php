<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('m_igi_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('m_igi_devices', 'dns_primary')) {
                $table->string('dns_primary')->nullable()->after('gateway');
            }
            if (!Schema::hasColumn('m_igi_devices', 'dns_secondary')) {
                $table->string('dns_secondary')->nullable()->after('dns_primary');
            }

            // Domain join status: NONE / WORKGROUP / DOMAIN
            if (!Schema::hasColumn('m_igi_devices', 'domain_join_status')) {
                $table->string('domain_join_status')->nullable()->after('os_version');
            }
            if (!Schema::hasColumn('m_igi_devices', 'domain_name')) {
                $table->string('domain_name')->nullable()->after('domain_join_status');
            }
            if (!Schema::hasColumn('m_igi_devices', 'workgroup_name')) {
                $table->string('workgroup_name')->nullable()->after('domain_name');
            }
        });
    }

    public function down()
    {
        Schema::table('m_igi_devices', function (Blueprint $table) {
            if (Schema::hasColumn('m_igi_devices', 'dns_secondary')) {
                $table->dropColumn('dns_secondary');
            }
            if (Schema::hasColumn('m_igi_devices', 'dns_primary')) {
                $table->dropColumn('dns_primary');
            }
            if (Schema::hasColumn('m_igi_devices', 'workgroup_name')) {
                $table->dropColumn('workgroup_name');
            }
            if (Schema::hasColumn('m_igi_devices', 'domain_name')) {
                $table->dropColumn('domain_name');
            }
            if (Schema::hasColumn('m_igi_devices', 'domain_join_status')) {
                $table->dropColumn('domain_join_status');
            }
        });
    }
};
