<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Audit Retention
    |--------------------------------------------------------------------------
    |
    | Tujuan: menjaga audit log tetap berguna tanpa membebani database.
    |
    | - Aksi sensitif disimpan penuh (tidak dipurge otomatis):
    |   contoh: reveal/rotate/deactivate secret.
    | - Aksi low-value dipangkas cepat: per account hanya simpan N terakhir.
    |
    */

    // Disimpan penuh (tidak dipangkas oleh mekanisme per-N)
    'sensitive_actions' => [
        'SECRET_REVEAL',
        'SECRET_ROTATE',
        'SECRET_DEACTIVATE',
        // aman untuk dianggap sensitif juga:
        'SECRET_ADD',
    ],

    // Dipangkas cepat (low-value)
    'low_value_actions' => [
        'ACCOUNT_DETAIL_VIEW',
    ],

    // Maksimal log low-value yang disimpan per account
    'low_value_keep_per_account' => 50,
];
