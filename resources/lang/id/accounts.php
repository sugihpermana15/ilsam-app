<?php

return [
    'index' => [
        'page_title' => 'Data Akun | IGI',
        'title_sub' => 'Dashboard Data Akun',
        'pagetitle' => 'Data Akun',
        'card_title' => 'Data Akun (Enterprise)',
        'add_account' => 'Tambah Akun',

        'filters' => [
            'plant_site' => 'Plant/Site',
            'plant_placeholder' => 'Contoh: Plant A',
            'category' => 'Kategori',
            'all' => 'Semua',
            'asset_code' => 'Kode Aset',
            'asset_code_placeholder' => 'Contoh: IGI-***-*****-*****',
            'location' => 'Lokasi',
            'location_placeholder' => 'Contoh: Gate / Office',
            'status' => 'Status',
            'apply' => 'Filter',
            'reset' => 'Reset',
        ],

        'empty' => [
            'title' => 'Belum ada data akun.',
            'hint' => 'Klik Tambah Akun untuk menambahkan data baru.',
        ],

        'table' => [
            'category' => 'Kategori',
            'ip' => 'IP (Local/Public)',
            'endpoint' => 'Endpoint',
            'username' => 'Username',
            'password' => 'Password',
            'mac_address' => 'MAC Address',
            'area_location' => 'Lokasi Area',
            'status' => 'Status',
            'notes' => 'Catatan',
            'action' => 'Aksi',
            'local' => 'Local',
            'public' => 'Public',
        ],

        'endpoint_labels' => [
            'management' => 'Management',
            'web' => 'Web',
        ],

        'actions' => [
            'detail' => 'Detail',
            'edit' => 'Edit',
            'delete' => 'Hapus',
            'open' => 'Buka',
            'open_local' => 'Buka IP Local',
            'open_public' => 'Buka IP Public',
            'toggle_dropdown' => 'Toggle Dropdown',
        ],

        'badge' => [
            'saved' => 'Tersimpan',
        ],

        'modals' => [
            'create_title' => 'Tambah Data Akun',
            'edit_title' => 'Ubah Data Akun',
        ],

        'form' => [
            'category' => 'Kategori',
            'select_category' => 'Pilih Kategori',
            'asset' => 'Aset',
            'select_asset' => 'Pilih Aset',
            'asset_required_hint' => 'Untuk kategori CCTV/Router-WiFi wajib pilih aset.',

            'status' => 'Status',
            'environment' => 'Lingkungan',
            'criticality' => 'Kritikalitas',

            'environment_options' => [
                'prod' => 'Prod',
                'nonprod' => 'Non-Prod',
                'internal' => 'Internal',
                'external' => 'External',
            ],

            'criticality_options' => [
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
            ],

            'vendor_installer' => 'Vendor/Installer',
            'department_owner' => 'Pemilik Departemen',
            'optional' => '(opsional)',

            'pick_category_hint' => 'Pilih Kategori untuk menampilkan form yang sesuai.',

            'general' => [
                'title' => 'Akun Umum (Email/NAS/Hotspot/dll)',
                'hint_create' => 'Isi bagian ini jika kategori bukan CCTV/Router-WiFi.',
                'hint_edit' => 'Gunakan untuk kategori selain CCTV/Router-WiFi. Password/secret diubah via Rotate pada halaman detail.',
                'username' => 'Username',
                'username_optional' => '(opsional)',
                'password_secret' => 'Password/Secret',
                'password_required_for_category' => 'Wajib untuk kategori ini',
                'password_use_rotate' => '(gunakan rotate di detail)',
                'current_username' => 'Username Saat Ini',
            ],

            'cctv' => [
                'title' => 'CCTV',
                'hint' => 'Isi bagian ini jika Kategori = CCTV.',
                'ip_local' => 'IP Lokal',
                'ip_public' => 'IP Publik',
                'web_port' => 'Port Web',
                'hikconnect_port' => 'Port HikConnect',
                'users_title' => 'Daftar User CCTV',
                'users_hint_create' => 'Tambahkan satu atau banyak user (tiap baris akan menjadi kredensial terpisah).',
                'users_hint_edit' => 'User yang sudah ada bisa dilihat/dinonaktifkan di halaman detail. Form ini hanya menambahkan user baru.',
                'add_user' => 'Tambah User',
                'bulk_paste' => 'Bulk Paste (opsional)',
                'bulk_placeholder' => "Format per baris: username|password|role (role opsional)\nContoh:\nadmin|P@ssw0rd|admin\noperator|P@ssw0rd|viewer",
                'parse' => 'Parse',
                'role_label' => 'Role/Label',
                'role_placeholder' => 'admin/viewer (opsional)',
                'username' => 'Username',
                'username_placeholder' => 'admin',
                'password' => 'Password',
                'password_required' => 'Wajib',
                'remove' => 'Hapus',
            ],

            'router' => [
                'title' => 'Router/WiFi',
                'hint' => 'Isi bagian ini jika Kategori = Router/WiFi.',
                'area_location' => 'Lokasi Area',
                'area_location_placeholder' => 'Contoh: Main Office',
                'mac_address' => 'Alamat MAC',
                'protocol' => 'Protokol',
                'ip_local' => 'IP Lokal',
                'ip_public' => 'IP Publik',
                'port' => 'Port',
                'default_username' => 'Username Bawaan',
                'default_password' => 'Password Bawaan',
                'current_username' => 'Username Saat Ini',
                'current_password' => 'Password Saat Ini',
            ],

            'notes' => 'Catatan',
            'close' => 'Tutup',
            'save' => 'Simpan',
            'save_changes' => 'Simpan Perubahan',
            'rotate_hint' => 'Untuk ganti password/secret gunakan fitur Rotate di halaman detail (audit tercatat).',
        ],

        'swal' => [
            'confirm_delete_title' => 'Yakin hapus data ini?',
            'confirm_delete_text' => 'Tindakan ini tidak bisa dibatalkan.',
            'confirm_delete_yes' => 'Ya, Hapus',
            'confirm_delete_cancel' => 'Batal',
            'fetch_account_error' => 'Gagal mengambil data account',
            'generic_error' => 'Terjadi kesalahan',
        ],
    ],

    'status' => [
        'active' => 'Aktif',
        'rotated' => 'Rotasi',
        'deprecated' => 'Tidak Digunakan',
    ],

    'show' => [
        'page_title' => 'Detail Akun | IGI',
        'title_sub' => 'Dashboard Data Akun',
        'pagetitle' => 'Detail Akun',

        'header' => [
            'title' => 'Akun #:id - :type',
            'asset_line' => 'Aset: :code / :name',
        ],

        'sections' => [
            'metadata' => 'Metadata',
            'endpoint' => 'Endpoint',
            'credentials' => 'Kredensial',
            'pending_approvals' => 'Persetujuan Pending',
            'notes' => 'Catatan',
            'audit_logs' => 'Log Audit (50 terakhir)',
        ],

        'empty' => [
            'endpoint' => 'Belum ada endpoint.',
            'credentials' => 'Belum ada kredensial.',
            'pending_approvals' => 'Tidak ada permintaan persetujuan.',
            'audit_logs' => 'Belum ada log audit untuk akun ini.',
        ],

        'labels' => [
            'asset' => 'Aset',
            'category' => 'Kategori',
            'status' => 'Status',
            'plant_site' => 'Plant/Site',
            'location' => 'Lokasi',
            'area_location' => 'Lokasi Area',
            'environment' => 'Lingkungan',
            'criticality' => 'Kritikalitas',
            'vendor_installer' => 'Vendor/Installer',
            'last_verified_at' => 'Terakhir Verifikasi',
            'verification_note_optional' => 'Catatan verifikasi (opsional)',
            'port' => 'Port',
            'username' => 'Username',
            'secret' => 'Secret',
        ],

        'actions' => [
            'verify' => 'Verifikasi',
        ],

        'approvals' => [
            'requester' => 'Pemohon',
            'secret' => 'Secret',
            'created_at' => 'Waktu',
            'reason' => 'Alasan',
            'approve' => 'Setujui',
        ],

        'secrets' => [
            'masked_note' => 'Password/secret selalu disamarkan. Menampilkan secret membutuhkan re-auth, dan (non Super Admin) perlu persetujuan.',
            'add_credential' => 'Tambah User/Kredensial',
            'add_credential_hint' => 'Tidak mem-rotate dan tidak menonaktifkan user lain. Cocok untuk CCTV yang punya banyak user.',
            'add_row' => 'Tambah Baris',
            'bulk_paste_label' => 'Bulk Paste (opsional)',
            'bulk_placeholder' => "Format per baris: username|password|role (role opsional)\nContoh:\nadmin|P@ssw0rd|admin\noperator|P@ssw0rd|viewer",
            'parse' => 'Parse',
            'add_reason_optional' => 'Alasan (opsional)',
            'save_credentials' => 'Simpan Kredensial',
            'inactive' => 'Nonaktif',
            'copy_username' => 'Salin Username',
            'reveal' => 'Tampilkan',
            'deactivate' => 'Nonaktifkan',
            'rotate_title' => 'Rotasi Secret',
            'kind_current' => 'current',
            'kind_default' => 'default',
            'label_optional' => 'label (opsional)',
            'username_optional' => 'username (opsional)',
            'new_secret' => 'secret baru',
            'rotate' => 'Rotasi',
            'reason_optional' => 'alasan rotasi (opsional)',
            'notes' => 'Catatan',

            'row' => [
                'role_label' => 'Role/Label',
                'role_placeholder' => 'admin/viewer/operator',
                'username' => 'Username',
                'username_placeholder' => 'admin',
                'password' => 'Password',
                'password_required' => 'Wajib',
                'remove' => 'Hapus',
            ],
        ],

        'reauth' => [
            'modal_title' => 'Tampilkan Secret',
            'title' => 'Re-auth',
            'subtitle' => 'Konfirmasi password untuk re-auth.',
            'password_confirm' => 'Konfirmasi password',
            'result_label' => 'Hasil',
            'result_empty' => '(kosong)',
            'audit_note' => 'Aksi reveal/copy akan tercatat di audit log.',
        ],

        'swal' => [
            'copied' => 'Tersalin',
            'copy_failed' => 'Gagal menyalin',
            'no_access' => 'Tidak punya akses.',

            'deactivate_title' => 'Nonaktifkan kredensial?',
            'deactivate_text' => 'Kredensial akan dinonaktifkan dan tidak bisa direveal lagi. Aksi ini tercatat di audit log.',
            'deactivate_yes' => 'Ya, nonaktifkan',
            'sent' => 'Terkirim',
            'approval_sent' => 'Permintaan persetujuan berhasil dikirim.',
            'approval_failed' => 'Gagal mengajukan persetujuan.',
            'reveal_failed' => 'Reveal gagal',

            'generic_error' => 'Terjadi kesalahan',

            'need_approval_title' => 'Perlu persetujuan',
            'need_approval_confirm' => 'Ajukan Persetujuan',
            'need_approval_input_label' => 'Alasan permintaan (opsional)',
            'need_approval_placeholder' => 'Contoh: troubleshooting kamera gate A',
        ],

        'endpoint' => [
            'open' => 'Buka',
            'open_local' => 'Buka IP Local',
            'open_public' => 'Buka IP Public',
            'toggle_dropdown' => 'Toggle Dropdown',
        ],

        'audit' => [
            'time' => 'Waktu',
            'actor' => 'Pelaku',
            'action' => 'Aksi',
            'result' => 'Hasil',
            'target' => 'Target',
            'reason' => 'Alasan',
        ],
    ],
];
