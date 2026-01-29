<?php

return [
    'network_error' => 'Terjadi gangguan jaringan. Silakan coba lagi.',
    'required' => 'Kolom ini wajib diisi.',

    'titles' => [
        '401' => 'Ilsam - Tidak Berwenang',
        '404' => 'Ilsam - Halaman Tidak Ditemukan',
        '500' => 'Ilsam - Gangguan Server',
    ],

    'pages' => [
        'common' => [
            'auth_bg_alt' => 'Latar Autentikasi',
            'vector_alt' => 'Gambar Ilustrasi',
        ],
        'actions' => [
            'back_to_home' => 'Kembali ke Beranda',
        ],
        '401' => [
            'headline_prefix' => 'Ups,',
            'headline' => 'Anda belum login!',
            'desc' => 'Sepertinya Anda tidak memiliki izin untuk melihat halaman ini. Silakan login untuk melanjutkan.',
        ],
        '404' => [
            'headline_prefix' => 'Oops!',
            'headline' => 'Halaman tidak ditemukan',
            'desc' => 'URL yang Anda akses tidak tersedia atau sudah dipindahkan.',
        ],
        '500' => [
            'headline_prefix' => 'Ups,',
            'headline' => 'terjadi kesalahan!',
            'desc' => 'Sepertinya ada kendala. Tim kami sedang menanganinya! Silakan coba lagi beberapa saat lagi.',
        ],
    ],
];
