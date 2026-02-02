<?php

return [
    'asset_categories' => [
        'page_title' => 'Master Kategori Aset',
        'subtitle' => 'Master Data Kategori Aset',
        'pagetitle' => 'Master Kategori Aset',
        'card_title' => 'Master Kategori Aset',
        'add_modal_title' => 'Tambah Kategori Aset',
        'edit_modal_title' => 'Edit Kategori Aset',
        'fields' => [
            'asset_code_prefix' => 'Prefix Kode Aset',
        ],
        'table' => [
            'asset_code_prefix' => 'Prefix Kode Aset',
        ],
        'hint_prefix' => 'Digunakan untuk format kode aset: IGI-{PREFIX}-...',
        'placeholders' => [
            'code' => 'mis: IT',
            'name' => 'mis: Information Technology',
            'prefix' => 'mis: IT',
        ],
    ],

    'asset_locations' => [
        'page_title' => 'Master Lokasi Aset',
        'subtitle' => 'Master Data Lokasi Aset',
        'pagetitle' => 'Master Lokasi Aset',
        'card_title' => 'Master Lokasi Aset',
        'add_modal_title' => 'Tambah Lokasi Aset',
        'edit_modal_title' => 'Edit Lokasi Aset',
        'fields' => [
            'location_name' => 'Nama Lokasi',
            'asset_code_prefix' => 'Kode Lokasi (Kode Aset)',
        ],
        'table' => [
            'asset_code_prefix' => 'Kode Lokasi (Kode Aset)',
        ],
        'hint_prefix' => 'Digunakan untuk format kode aset: IGI-..-{KODE}-...',
        'placeholders' => [
            'name' => 'mis: Jababeka',
            'prefix' => 'mis: 01',
        ],
    ],

    'asset_uoms' => [
        'page_title' => 'Master Satuan Aset',
        'subtitle' => 'Master Data Satuan Aset',
        'pagetitle' => 'Master Satuan Aset',
        'card_title' => 'Master Satuan Aset',
        'add_modal_title' => 'Tambah Satuan Aset',
        'edit_modal_title' => 'Edit Satuan Aset',
        'placeholders' => [
            'name' => 'mis: pcs',
        ],
    ],

    'asset_vendors' => [
        'page_title' => 'Master Vendor Aset',
        'subtitle' => 'Master Data Vendor Aset',
        'pagetitle' => 'Master Vendor Aset',
        'card_title' => 'Master Vendor/Supplier Aset',
        'add_modal_title' => 'Tambah Vendor/Supplier',
        'edit_modal_title' => 'Edit Vendor/Supplier',
        'placeholders' => [
            'name' => 'mis: PT ABC',
        ],
    ],

    'account_types' => [
        'page_title' => 'Master Kategori Akun',
        'subtitle' => 'Master Data Kategori Akun',
        'pagetitle' => 'Master Kategori Akun',
        'card_title' => 'Master Kategori Akun',
        'add_modal_title' => 'Tambah Kategori Akun',
        'edit_modal_title' => 'Edit Kategori Akun',
        'hint' => 'Nama kategori akan muncul di dropdown Data Akun.',
        'placeholders' => [
            'name' => 'mis: Email / NAS / Hotspot',
        ],
    ],

    'plant_sites' => [
        'page_title' => 'Master Plant/Site',
        'subtitle' => 'Master Data Plant/Site',
        'pagetitle' => 'Master Plant/Site',
        'card_title' => 'Master Plant/Site',
        'description' => 'Dipakai untuk dropdown Plant/Site pada modul Documents.',
        'add_modal_title' => 'Tambah Plant/Site',
        'edit_modal_title' => 'Edit Plant/Site',
        'fields' => [
            'plant_site' => 'Plant/Site',
            'name_optional' => 'Nama (opsional)',
            'building' => 'Building',
            'floor' => 'Floor',
            'room_rack' => 'Room/Rack',
        ],
        'table' => [
            'plant_site' => 'Plant/Site',
            'name' => 'Nama',
            'building' => 'Building',
            'floor' => 'Floor',
            'room_rack' => 'Room/Rack',
        ],
        'placeholders' => [
            'plant_site' => 'mis: Jababeka',
            'name' => 'mis: HO / Gudang / Site A',
        ],
    ],

    'uniform_categories' => [
        'page_title' => 'Master Kategori Seragam',
        'subtitle' => 'Master Data Kategori Seragam',
        'pagetitle' => 'Master Kategori Seragam',
        'card_title' => 'Master Kategori Seragam',
        'add_modal_title' => 'Tambah Kategori',
        'edit_modal_title' => 'Edit Kategori',
    ],

    'uniform_colors' => [
        'page_title' => 'Master Warna Seragam',
        'subtitle' => 'Master Data Warna Seragam',
        'pagetitle' => 'Master Warna Seragam',
        'card_title' => 'Master Warna Seragam',
        'add_modal_title' => 'Tambah Warna',
        'edit_modal_title' => 'Edit Warna',
    ],

    'uniform_item_names' => [
        'page_title' => 'Master Nama Item Seragam',
        'subtitle' => 'Master Data Nama Item Seragam',
        'pagetitle' => 'Master Nama Item Seragam',
        'card_title' => 'Master Nama Item Seragam',
        'add_modal_title' => 'Tambah Nama Item',
        'edit_modal_title' => 'Edit Nama Item',
    ],

    'uniform_uoms' => [
        'page_title' => 'Master UOM Seragam',
        'subtitle' => 'Master Data UOM Seragam',
        'pagetitle' => 'Master UOM Seragam',
        'card_title' => 'Master UOM Seragam',
        'add_modal_title' => 'Tambah UOM',
        'edit_modal_title' => 'Edit UOM',
        'hint_same_as_code' => 'Jika kosong, akan disamakan dengan kode.',
        'placeholders' => [
            'code' => 'mis: pcs',
            'name' => 'mis: Pieces',
        ],
    ],

    'uniform_sizes' => [
        'page_title' => 'Master Ukuran Seragam',
        'subtitle' => 'Master Data Ukuran Seragam',
        'pagetitle' => 'Master Ukuran Seragam',
        'card_title' => 'Master Ukuran Seragam',
        'add_button' => 'Tambah Ukuran',
        'add_modal_title' => 'Tambah Ukuran',
        'edit_modal_title' => 'Edit Ukuran',
        'delete_tooltip' => 'Hapus',
        'hint_same_as_code' => 'Jika kosong, akan disamakan dengan kode.',
        'delete' => [
            'title' => 'Hapus ukuran?',
            'text' => 'Ukuran hanya bisa dihapus jika belum dipakai oleh item.',
            'confirm' => 'Ya, hapus',
        ],
        'placeholders' => [
            'code' => 'mis: XL',
            'name' => 'mis: Extra Large',
        ],
    ],
];
