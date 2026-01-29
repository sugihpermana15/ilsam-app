<?php

return [
    'asset_categories' => [
        'page_title' => '자산 카테고리 마스터',
        'subtitle' => '자산 카테고리 마스터 데이터',
        'pagetitle' => '자산 카테고리 마스터',
        'card_title' => '자산 카테고리 마스터',
        'add_modal_title' => '자산 카테고리 추가',
        'edit_modal_title' => '자산 카테고리 수정',
        'fields' => [
            'asset_code_prefix' => '자산 코드 접두어',
        ],
        'table' => [
            'asset_code_prefix' => '자산 코드 접두어',
        ],
        'hint_prefix' => '자산 코드 형식에 사용: IGI-{PREFIX}-...',
        'placeholders' => [
            'code' => '예: IT',
            'name' => '예: Information Technology',
            'prefix' => '예: IT',
        ],
    ],

    'asset_locations' => [
        'page_title' => '자산 위치 마스터',
        'subtitle' => '자산 위치 마스터 데이터',
        'pagetitle' => '자산 위치 마스터',
        'card_title' => '자산 위치 마스터',
        'add_modal_title' => '자산 위치 추가',
        'edit_modal_title' => '자산 위치 수정',
        'fields' => [
            'location_name' => '위치명',
            'asset_code_prefix' => '위치 코드(자산 코드)',
        ],
        'table' => [
            'asset_code_prefix' => '위치 코드(자산 코드)',
        ],
        'hint_prefix' => '자산 코드 형식에 사용: IGI-..-{CODE}-...',
        'placeholders' => [
            'name' => '예: Jababeka',
            'prefix' => '예: 01',
        ],
    ],

    'asset_uoms' => [
        'page_title' => '자산 단위 마스터',
        'subtitle' => '자산 단위 마스터 데이터',
        'pagetitle' => '자산 단위 마스터',
        'card_title' => '자산 단위 마스터',
        'add_modal_title' => '자산 단위 추가',
        'edit_modal_title' => '자산 단위 수정',
        'placeholders' => [
            'name' => '예: pcs',
        ],
    ],

    'asset_vendors' => [
        'page_title' => '자산 벤더 마스터',
        'subtitle' => '자산 벤더 마스터 데이터',
        'pagetitle' => '자산 벤더 마스터',
        'card_title' => '자산 벤더/공급업체 마스터',
        'add_modal_title' => '벤더/공급업체 추가',
        'edit_modal_title' => '벤더/공급업체 수정',
        'placeholders' => [
            'name' => '예: PT ABC',
        ],
    ],

    'account_types' => [
        'page_title' => '계정 카테고리 마스터',
        'subtitle' => '계정 카테고리 마스터 데이터',
        'pagetitle' => '계정 카테고리 마스터',
        'card_title' => '계정 카테고리 마스터',
        'add_modal_title' => '계정 카테고리 추가',
        'edit_modal_title' => '계정 카테고리 수정',
        'hint' => '카테고리 이름은 계정 데이터 드롭다운에 표시됩니다.',
        'placeholders' => [
            'name' => '예: Email / NAS / Hotspot',
        ],
    ],

    'plant_sites' => [
        'page_title' => 'Plant/Site 마스터',
        'subtitle' => 'Plant/Site 마스터 데이터',
        'pagetitle' => 'Plant/Site 마스터',
        'card_title' => 'Plant/Site 마스터',
        'description' => 'Documents 모듈의 Plant/Site 드롭다운에 사용됩니다.',
        'add_modal_title' => 'Plant/Site 추가',
        'edit_modal_title' => 'Plant/Site 수정',
        'fields' => [
            'plant_site' => 'Plant/Site',
            'name_optional' => '이름(선택)',
            'building' => '건물',
            'floor' => '층',
            'room_rack' => '룸/랙',
        ],
        'table' => [
            'plant_site' => 'Plant/Site',
            'name' => '이름',
            'building' => '건물',
            'floor' => '층',
            'room_rack' => '룸/랙',
        ],
        'placeholders' => [
            'plant_site' => '예: Jababeka',
            'name' => '예: HO / 창고 / Site A',
        ],
    ],

    'uniform_categories' => [
        'page_title' => '유니폼 카테고리 마스터',
        'subtitle' => '유니폼 카테고리 마스터 데이터',
        'pagetitle' => '유니폼 카테고리 마스터',
        'card_title' => '유니폼 카테고리 마스터',
        'add_modal_title' => '카테고리 추가',
        'edit_modal_title' => '카테고리 수정',
    ],

    'uniform_colors' => [
        'page_title' => '유니폼 색상 마스터',
        'subtitle' => '유니폼 색상 마스터 데이터',
        'pagetitle' => '유니폼 색상 마스터',
        'card_title' => '유니폼 색상 마스터',
        'add_modal_title' => '색상 추가',
        'edit_modal_title' => '색상 수정',
    ],

    'uniform_item_names' => [
        'page_title' => '유니폼 아이템명 마스터',
        'subtitle' => '유니폼 아이템명 마스터 데이터',
        'pagetitle' => '유니폼 아이템명 마스터',
        'card_title' => '유니폼 아이템명 마스터',
        'add_modal_title' => '아이템명 추가',
        'edit_modal_title' => '아이템명 수정',
    ],

    'uniform_uoms' => [
        'page_title' => '유니폼 UOM 마스터',
        'subtitle' => '유니폼 UOM 마스터 데이터',
        'pagetitle' => '유니폼 UOM 마스터',
        'card_title' => '유니폼 UOM 마스터',
        'add_modal_title' => 'UOM 추가',
        'edit_modal_title' => 'UOM 수정',
        'hint_same_as_code' => '비워두면 코드와 동일하게 저장됩니다.',
        'placeholders' => [
            'code' => '예: pcs',
            'name' => '예: Pieces',
        ],
    ],

    'uniform_sizes' => [
        'page_title' => '유니폼 사이즈 마스터',
        'subtitle' => '유니폼 사이즈 마스터 데이터',
        'pagetitle' => '유니폼 사이즈 마스터',
        'card_title' => '유니폼 사이즈 마스터',
        'add_button' => '사이즈 추가',
        'add_modal_title' => '사이즈 추가',
        'edit_modal_title' => '사이즈 수정',
        'delete_tooltip' => '삭제',
        'hint_same_as_code' => '비워두면 코드와 동일하게 저장됩니다.',
        'delete' => [
            'title' => '사이즈를 삭제할까요?',
            'text' => '사이즈는 아이템에 사용되기 전까지만 삭제할 수 있습니다.',
            'confirm' => '예, 삭제',
        ],
        'placeholders' => [
            'code' => '예: XL',
            'name' => '예: Extra Large',
        ],
    ],
];
