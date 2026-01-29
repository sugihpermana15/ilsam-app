<?php

return [
    'network_error' => '네트워크 오류입니다. 다시 시도해 주세요.',
    'required' => '필수 입력 항목입니다.',

    'titles' => [
        '401' => 'Ilsam - 권한 없음',
        '404' => 'Ilsam - 페이지를 찾을 수 없음',
        '500' => 'Ilsam - 서버 오류',
    ],

    'pages' => [
        'common' => [
            'auth_bg_alt' => '인증 배경',
            'vector_alt' => '벡터 이미지',
        ],
        'actions' => [
            'back_to_home' => '홈으로 돌아가기',
        ],
        '401' => [
            'headline_prefix' => '이런,',
            'headline' => '로그인이 필요합니다!',
            'desc' => '이 페이지를 볼 권한이 없습니다. 계속하려면 로그인해 주세요.',
        ],
        '404' => [
            'headline_prefix' => '앗!',
            'headline' => '페이지를 찾을 수 없습니다',
            'desc' => '요청하신 URL이 없거나 이동되었습니다.',
        ],
        '500' => [
            'headline_prefix' => '이런,',
            'headline' => '문제가 발생했습니다!',
            'desc' => '일시적인 오류가 발생했습니다. 현재 처리 중입니다. 잠시 후 다시 시도해 주세요.',
        ],
    ],
];
