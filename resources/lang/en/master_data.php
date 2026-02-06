<?php

return [
    'asset_categories' => [
        'page_title' => 'Asset Category Master',
        'subtitle' => 'Asset Category Master Data',
        'pagetitle' => 'Asset Category Master',
        'card_title' => 'Asset Category Master',
        'add_modal_title' => 'Add Asset Category',
        'edit_modal_title' => 'Edit Asset Category',
        'fields' => [
            'asset_code_prefix' => 'Asset Code Prefix',
        ],
        'table' => [
            'asset_code_prefix' => 'Asset Code Prefix',
        ],
        'hint_prefix' => 'Used for asset code format: IGI-{PREFIX}-...',
        'placeholders' => [
            'code' => 'e.g.: IT',
            'name' => 'e.g.: Information Technology',
            'prefix' => 'e.g.: IT',
        ],
    ],

    'asset_locations' => [
        'page_title' => 'Asset Location Master',
        'subtitle' => 'Asset Location Master Data',
        'pagetitle' => 'Asset Location Master',
        'card_title' => 'Asset Location Master',
        'add_modal_title' => 'Add Asset Location',
        'edit_modal_title' => 'Edit Asset Location',
        'fields' => [
            'location_name' => 'Location Name',
            'asset_code_prefix' => 'Location Code (Asset Code)',
        ],
        'table' => [
            'asset_code_prefix' => 'Location Code (Asset Code)',
        ],
        'hint_prefix' => 'Used for asset code format: IGI-..-{CODE}-...',
        'placeholders' => [
            'name' => 'e.g.: Jababeka',
            'prefix' => 'e.g.: 01',
        ],
    ],

    'asset_uoms' => [
        'page_title' => 'Asset Unit Master',
        'subtitle' => 'Asset Unit Master Data',
        'pagetitle' => 'Asset Unit Master',
        'card_title' => 'Asset Unit Master',
        'add_modal_title' => 'Add Asset Unit',
        'edit_modal_title' => 'Edit Asset Unit',
        'placeholders' => [
            'name' => 'e.g.: pcs',
        ],
    ],

    'asset_vendors' => [
        'page_title' => 'Asset Vendor Master',
        'subtitle' => 'Asset Vendor Master Data',
        'pagetitle' => 'Asset Vendor Master',
        'card_title' => 'Asset Vendor/Supplier Master',
        'add_modal_title' => 'Add Vendor/Supplier',
        'edit_modal_title' => 'Edit Vendor/Supplier',
        'placeholders' => [
            'name' => 'e.g.: PT ABC',
        ],
    ],

    'account_types' => [
        'page_title' => 'Account Category Master',
        'subtitle' => 'Account Category Master Data',
        'pagetitle' => 'Account Category Master',
        'card_title' => 'Account Category Master',
        'add_modal_title' => 'Add Account Category',
        'edit_modal_title' => 'Edit Account Category',
        'hint' => 'Category name will appear in the Account Data dropdown.',
        'placeholders' => [
            'name' => 'e.g.: Email / NAS / Hotspot',
        ],
    ],

    'plant_sites' => [
        'page_title' => 'Plant/Site Master',
        'subtitle' => 'Plant/Site Master Data',
        'pagetitle' => 'Plant/Site Master',
        'card_title' => 'Plant/Site Master',
        'description' => 'Used for Plant/Site dropdown in Documents module.',
        'add_modal_title' => 'Add Plant/Site',
        'edit_modal_title' => 'Edit Plant/Site',
        'fields' => [
            'plant_site' => 'Plant/Site',
            'name_optional' => 'Name (optional)',
            'building' => 'Building',
            'floor' => 'Floor',
            'room_rack' => 'Room/Rack',
        ],
        'table' => [
            'plant_site' => 'Plant/Site',
            'name' => 'Name',
            'building' => 'Building',
            'floor' => 'Floor',
            'room_rack' => 'Room/Rack',
        ],
        'placeholders' => [
            'plant_site' => 'e.g.: Jababeka',
            'name' => 'e.g.: HO / Warehouse / Site A',
        ],
    ],

 
];
