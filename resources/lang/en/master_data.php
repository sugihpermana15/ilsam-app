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

    'uniform_categories' => [
        'page_title' => 'Uniform Category Master',
        'subtitle' => 'Uniform Category Master Data',
        'pagetitle' => 'Uniform Category Master',
        'card_title' => 'Uniform Category Master',
        'add_modal_title' => 'Add Category',
        'edit_modal_title' => 'Edit Category',
    ],

    'uniform_colors' => [
        'page_title' => 'Uniform Color Master',
        'subtitle' => 'Uniform Color Master Data',
        'pagetitle' => 'Uniform Color Master',
        'card_title' => 'Uniform Color Master',
        'add_modal_title' => 'Add Color',
        'edit_modal_title' => 'Edit Color',
    ],

    'uniform_item_names' => [
        'page_title' => 'Uniform Item Name Master',
        'subtitle' => 'Uniform Item Name Master Data',
        'pagetitle' => 'Uniform Item Name Master',
        'card_title' => 'Uniform Item Name Master',
        'add_modal_title' => 'Add Item Name',
        'edit_modal_title' => 'Edit Item Name',
    ],

    'uniform_uoms' => [
        'page_title' => 'Uniform UOM Master',
        'subtitle' => 'Uniform UOM Master Data',
        'pagetitle' => 'Uniform UOM Master',
        'card_title' => 'Uniform UOM Master',
        'add_modal_title' => 'Add UOM',
        'edit_modal_title' => 'Edit UOM',
        'hint_same_as_code' => 'If empty, it will be the same as code.',
        'placeholders' => [
            'code' => 'e.g.: pcs',
            'name' => 'e.g.: Pieces',
        ],
    ],

    'uniform_sizes' => [
        'page_title' => 'Uniform Size Master',
        'subtitle' => 'Uniform Size Master Data',
        'pagetitle' => 'Uniform Size Master',
        'card_title' => 'Uniform Size Master',
        'add_button' => 'Add Size',
        'add_modal_title' => 'Add Size',
        'edit_modal_title' => 'Edit Size',
        'delete_tooltip' => 'Delete',
        'hint_same_as_code' => 'If empty, it will be the same as code.',
        'delete' => [
            'title' => 'Delete size?',
            'text' => 'Size can only be deleted if it has not been used by any item.',
            'confirm' => 'Yes, delete',
        ],
        'placeholders' => [
            'code' => 'e.g.: XL',
            'name' => 'e.g.: Extra Large',
        ],
    ],
];
