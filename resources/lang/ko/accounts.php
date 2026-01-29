<?php

return [
    'index' => [
        'page_title' => '계정 데이터 | IGI',
        'title_sub' => '계정 데이터 대시보드',
        'pagetitle' => '계정 데이터',
        'card_title' => '계정 데이터 (Enterprise)',
        'add_account' => '계정 추가',

        'filters' => [
            'plant_site' => '플랜트/사이트',
            'plant_placeholder' => '예: Plant A',
            'category' => '카테고리',
            'all' => '전체',
            'asset_code' => '자산 코드',
            'asset_code_placeholder' => '예: IGI-***-*****-*****',
            'location' => '위치',
            'location_placeholder' => '예: Gate / Office',
            'status' => '상태',
            'apply' => '필터',
            'reset' => '초기화',
        ],

        'empty' => [
            'title' => '계정 데이터가 없습니다.',
            'hint' => '새 데이터를 추가하려면 계정 추가를 클릭하세요.',
        ],

        'table' => [
            'category' => '카테고리',
            'ip' => 'IP (Local/Public)',
            'endpoint' => '엔드포인트',
            'username' => '사용자명',
            'password' => '비밀번호',
            'mac_address' => 'MAC 주소',
            'area_location' => '지역 위치',
            'status' => '상태',
            'notes' => '비고',
            'action' => '작업',
            'local' => 'Local',
            'public' => 'Public',
        ],

        'endpoint_labels' => [
            'management' => 'Management',
            'web' => 'Web',
        ],

        'actions' => [
            'detail' => '상세',
            'edit' => '편집',
            'delete' => '삭제',
            'open' => '열기',
            'open_local' => 'Local IP 열기',
            'open_public' => 'Public IP 열기',
            'toggle_dropdown' => '드롭다운 토글',
        ],

        'badge' => [
            'saved' => '저장됨',
        ],

        'modals' => [
            'create_title' => '계정 데이터 추가',
            'edit_title' => '계정 데이터 수정',
        ],

        'form' => [
            'category' => '카테고리',
            'select_category' => '카테고리 선택',
            'asset' => '자산',
            'select_asset' => '자산 선택',
            'asset_required_hint' => 'CCTV/Router-WiFi 카테고리는 자산 선택이 필요합니다.',

            'status' => '상태',
            'environment' => '환경',
            'criticality' => '중요도',

            'environment_options' => [
                'prod' => 'Prod',
                'nonprod' => 'Non-Prod',
                'internal' => 'Internal',
                'external' => 'External',
            ],

            'criticality_options' => [
                'low' => '낮음',
                'medium' => '보통',
                'high' => '높음',
            ],

            'vendor_installer' => '공급업체/설치업체',
            'department_owner' => '소유 부서',
            'optional' => '(선택)',

            'pick_category_hint' => '적절한 양식을 보려면 카테고리를 선택하세요.',

            'general' => [
                'title' => '일반 계정 (Email/NAS/Hotspot 등)',
                'hint_create' => '카테고리가 CCTV/Router-WiFi가 아닌 경우 이 섹션을 입력하세요.',
                'hint_edit' => 'CCTV/Router-WiFi 외 카테고리에 사용합니다. 비밀번호/시크릿 변경은 상세 페이지의 Rotate를 사용하세요.',
                'username' => '사용자명',
                'username_optional' => '(선택)',
                'password_secret' => '비밀번호/시크릿',
                'password_required_for_category' => '해당 카테고리에 필수',
                'password_use_rotate' => '(상세에서 rotate 사용)',
                'current_username' => '현재 사용자명',
            ],

            'cctv' => [
                'title' => 'CCTV',
                'hint' => '카테고리 = CCTV 인 경우 이 섹션을 입력하세요.',
                'ip_local' => 'Local IP',
                'ip_public' => 'Public IP',
                'web_port' => '웹 포트',
                'hikconnect_port' => 'HikConnect 포트',
                'users_title' => 'CCTV 사용자 목록',
                'users_hint_create' => '사용자를 하나 이상 추가할 수 있습니다(각 행은 별도 자격 증명으로 저장).',
                'users_hint_edit' => '기존 사용자는 상세 페이지에서 확인/비활성화할 수 있습니다. 이 폼은 새 사용자만 추가합니다.',
                'add_user' => '사용자 추가',
                'bulk_paste' => '일괄 붙여넣기 (선택)',
                'bulk_placeholder' => "행별 형식: username|password|role (role 선택)\n예:\nadmin|P@ssw0rd|admin\noperator|P@ssw0rd|viewer",
                'parse' => '파싱',
                'role_label' => 'Role/Label',
                'role_placeholder' => 'admin/viewer (선택)',
                'username' => '사용자명',
                'username_placeholder' => 'admin',
                'password' => '비밀번호',
                'password_required' => '필수',
                'remove' => '삭제',
            ],

            'router' => [
                'title' => 'Router/WiFi',
                'hint' => '카테고리 = Router/WiFi 인 경우 이 섹션을 입력하세요.',
                'area_location' => '지역 위치',
                'area_location_placeholder' => '예: Main Office',
                'mac_address' => 'MAC 주소',
                'protocol' => '프로토콜',
                'ip_local' => 'Local IP',
                'ip_public' => 'Public IP',
                'port' => '포트',
                'default_username' => '기본 사용자명',
                'default_password' => '기본 비밀번호',
                'current_username' => '현재 사용자명',
                'current_password' => '현재 비밀번호',
            ],

            'notes' => '비고',
            'close' => '닫기',
            'save' => '저장',
            'save_changes' => '변경 저장',
            'rotate_hint' => '비밀번호/시크릿 변경은 상세 페이지의 Rotate 기능을 사용하세요(감사 로그 기록).',
        ],

        'swal' => [
            'confirm_delete_title' => '이 데이터를 삭제하시겠습니까?',
            'confirm_delete_text' => '이 작업은 되돌릴 수 없습니다.',
            'confirm_delete_yes' => '예, 삭제',
            'confirm_delete_cancel' => '취소',
            'fetch_account_error' => '계정 데이터를 불러오지 못했습니다',
            'generic_error' => '오류가 발생했습니다',
        ],
    ],

    'status' => [
        'active' => '활성',
        'rotated' => '교체됨',
        'deprecated' => '사용 안 함',
    ],

    'show' => [
        'page_title' => '계정 상세 | IGI',
        'title_sub' => '계정 데이터 대시보드',
        'pagetitle' => '계정 상세',

        'header' => [
            'title' => '계정 #:id - :type',
            'asset_line' => '자산: :code / :name',
        ],

        'sections' => [
            'metadata' => '메타데이터',
            'endpoint' => '엔드포인트',
            'credentials' => '자격 증명',
            'pending_approvals' => '대기 중인 승인',
            'notes' => '비고',
            'audit_logs' => '감사 로그(최근 50건)',
        ],

        'empty' => [
            'endpoint' => '엔드포인트가 없습니다.',
            'credentials' => '자격 증명이 없습니다.',
            'pending_approvals' => '승인 요청이 없습니다.',
            'audit_logs' => '이 계정에 대한 감사 로그가 없습니다.',
        ],

        'labels' => [
            'asset' => '자산',
            'category' => '카테고리',
            'status' => '상태',
            'plant_site' => '플랜트/사이트',
            'location' => '위치',
            'area_location' => '지역 위치',
            'environment' => '환경',
            'criticality' => '중요도',
            'vendor_installer' => '공급업체/설치업체',
            'last_verified_at' => '마지막 검증',
            'verification_note_optional' => '검증 메모(선택)',
            'port' => '포트',
            'username' => '사용자명',
            'secret' => '시크릿',
        ],

        'actions' => [
            'verify' => '검증',
        ],

        'approvals' => [
            'requester' => '요청자',
            'secret' => '시크릿',
            'created_at' => '시간',
            'reason' => '사유',
            'approve' => '승인',
        ],

        'secrets' => [
            'masked_note' => '비밀번호/시크릿은 항상 마스킹됩니다. 시크릿 표시에는 재인증이 필요하며, (Super Admin이 아닌 경우) 승인이 필요합니다.',
            'add_credential' => '사용자/자격 증명 추가',
            'add_credential_hint' => 'Rotate 하지 않고 다른 사용자를 비활성화하지 않습니다. 여러 사용자가 있는 CCTV에 적합합니다.',
            'add_row' => '행 추가',
            'bulk_paste_label' => '일괄 붙여넣기 (선택)',
            'bulk_placeholder' => "행별 형식: username|password|role (role 선택)\n예:\nadmin|P@ssw0rd|admin\noperator|P@ssw0rd|viewer",
            'parse' => '파싱',
            'add_reason_optional' => '사유(선택)',
            'save_credentials' => '자격 증명 저장',
            'inactive' => '비활성',
            'copy_username' => '사용자명 복사',
            'reveal' => '표시',
            'deactivate' => '비활성화',
            'rotate_title' => '시크릿 교체',
            'kind_current' => 'current',
            'kind_default' => 'default',
            'label_optional' => 'label (선택)',
            'username_optional' => 'username (선택)',
            'new_secret' => '새 시크릿',
            'rotate' => '교체',
            'reason_optional' => '교체 사유(선택)',
            'notes' => '비고',

            'row' => [
                'role_label' => 'Role/Label',
                'role_placeholder' => 'admin/viewer/operator',
                'username' => '사용자명',
                'username_placeholder' => 'admin',
                'password' => '비밀번호',
                'password_required' => '필수',
                'remove' => '삭제',
            ],
        ],

        'reauth' => [
            'modal_title' => '시크릿 표시',
            'title' => '재인증',
            'subtitle' => '재인증을 위해 비밀번호를 확인하세요.',
            'password_confirm' => '비밀번호 확인',
            'result_label' => '결과',
            'result_empty' => '(비어 있음)',
            'audit_note' => '표시/복사 작업은 감사 로그에 기록됩니다.',
        ],

        'swal' => [
            'copied' => '복사됨',
            'copy_failed' => '복사 실패',
            'no_access' => '권한이 없습니다.',

            'deactivate_title' => '자격 증명을 비활성화할까요?',
            'deactivate_text' => '자격 증명은 비활성화되며 다시 표시할 수 없습니다. 이 작업은 감사 로그에 기록됩니다.',
            'deactivate_yes' => '예, 비활성화',
            'sent' => '전송됨',
            'approval_sent' => '승인 요청이 전송되었습니다.',
            'approval_failed' => '승인 요청 제출에 실패했습니다.',
            'reveal_failed' => '표시에 실패했습니다',

            'generic_error' => '오류가 발생했습니다',

            'need_approval_title' => '승인이 필요합니다',
            'need_approval_confirm' => '승인 요청',
            'need_approval_input_label' => '요청 사유(선택)',
            'need_approval_placeholder' => '예: CCTV gate A 점검',
        ],

        'endpoint' => [
            'open' => '열기',
            'open_local' => 'Local IP 열기',
            'open_public' => 'Public IP 열기',
            'toggle_dropdown' => '드롭다운 토글',
        ],

        'audit' => [
            'time' => '시간',
            'actor' => '실행자',
            'action' => '작업',
            'result' => '결과',
            'target' => '대상',
            'reason' => '사유',
        ],
    ],
];
