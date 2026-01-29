<?php

return [
    'index' => [
        'page_title' => 'Account Data | IGI',
        'title_sub' => 'Account Data Dashboard',
        'pagetitle' => 'Account Data',
        'card_title' => 'Account Data (Enterprise)',
        'add_account' => 'Add Account',

        'filters' => [
            'plant_site' => 'Plant/Site',
            'plant_placeholder' => 'e.g.: Plant A',
            'category' => 'Category',
            'all' => 'All',
            'asset_code' => 'Asset Code',
            'asset_code_placeholder' => 'Example: IGI-***-*****-*****',
            'location' => 'Location',
            'location_placeholder' => 'Example: Gate / Office',
            'status' => 'Status',
            'apply' => 'Filter',
            'reset' => 'Reset',
        ],

        'empty' => [
            'title' => 'No account data yet.',
            'hint' => 'Click Add Account to add new data.',
        ],

        'table' => [
            'category' => 'Category',
            'ip' => 'IP (Local/Public)',
            'endpoint' => 'Endpoint',
            'username' => 'Username',
            'password' => 'Password',
            'mac_address' => 'MAC Address',
            'area_location' => 'Area Location',
            'status' => 'Status',
            'notes' => 'Notes',
            'action' => 'Action',
            'local' => 'Local',
            'public' => 'Public',
        ],

        'endpoint_labels' => [
            'management' => 'Management',
            'web' => 'Web',
        ],

        'actions' => [
            'detail' => 'Details',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'open' => 'Open',
            'open_local' => 'Open Local IP',
            'open_public' => 'Open Public IP',
            'toggle_dropdown' => 'Toggle Dropdown',
        ],

        'badge' => [
            'saved' => 'Saved',
        ],

        'modals' => [
            'create_title' => 'Add Account Data',
            'edit_title' => 'Edit Account Data',
        ],

        'form' => [
            'category' => 'Category',
            'select_category' => 'Select Category',
            'asset' => 'Asset',
            'select_asset' => 'Select Asset',
            'asset_required_hint' => 'For CCTV/Router-WiFi category, asset is required.',

            'status' => 'Status',
            'environment' => 'Environment',
            'criticality' => 'Criticality',

            'environment_options' => [
                'prod' => 'Prod',
                'nonprod' => 'Non-Prod',
                'internal' => 'Internal',
                'external' => 'External',
            ],

            'criticality_options' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
            ],

            'vendor_installer' => 'Vendor/Installer',
            'department_owner' => 'Department Owner',
            'optional' => '(optional)',

            'pick_category_hint' => 'Select Category to show the appropriate form.',

            'general' => [
                'title' => 'General Account (Email/NAS/Hotspot/etc.)',
                'hint_create' => 'Fill this section if the category is not CCTV/Router-WiFi.',
                'hint_edit' => 'Use for categories other than CCTV/Router-WiFi. Password/secret is changed via Rotate on the details page.',
                'username' => 'Username',
                'username_optional' => '(optional)',
                'password_secret' => 'Password/Secret',
                'password_required_for_category' => 'Required for this category',
                'password_use_rotate' => '(use rotate on details)',
                'current_username' => 'Current Username',
            ],

            'cctv' => [
                'title' => 'CCTV',
                'hint' => 'Fill this section if Category = CCTV.',
                'ip_local' => 'Local IP',
                'ip_public' => 'Public IP',
                'web_port' => 'Web Port',
                'hikconnect_port' => 'HikConnect Port',
                'users_title' => 'CCTV Users',
                'users_hint_create' => 'Add one or multiple users (each row becomes a separate credential).',
                'users_hint_edit' => 'Existing users can be viewed/deactivated on the details page. This form only adds new users.',
                'add_user' => 'Add User',
                'bulk_paste' => 'Bulk Paste (optional)',
                'bulk_placeholder' => "Format per line: username|password|role (role optional)\nExample:\nadmin|P@ssw0rd|admin\noperator|P@ssw0rd|viewer",
                'parse' => 'Parse',
                'role_label' => 'Role/Label',
                'role_placeholder' => 'admin/viewer (optional)',
                'username' => 'Username',
                'username_placeholder' => 'admin',
                'password' => 'Password',
                'password_required' => 'Required',
                'remove' => 'Remove',
            ],

            'router' => [
                'title' => 'Router/WiFi',
                'hint' => 'Fill this section if Category = Router/WiFi.',
                'area_location' => 'Area Location',
                'area_location_placeholder' => 'Example: Main Office',
                'mac_address' => 'MAC Address',
                'protocol' => 'Protocol',
                'ip_local' => 'Local IP',
                'ip_public' => 'Public IP',
                'port' => 'Port',
                'default_username' => 'Default Username',
                'default_password' => 'Default Password',
                'current_username' => 'Current Username',
                'current_password' => 'Current Password',
            ],

            'notes' => 'Notes',
            'close' => 'Close',
            'save' => 'Save',
            'save_changes' => 'Save Changes',
            'rotate_hint' => 'To change password/secret, use Rotate on the details page (audit is logged).',
        ],

        'swal' => [
            'confirm_delete_title' => 'Delete this data?',
            'confirm_delete_text' => 'This action cannot be undone.',
            'confirm_delete_yes' => 'Yes, Delete',
            'confirm_delete_cancel' => 'Cancel',
            'fetch_account_error' => 'Failed to fetch account data',
            'generic_error' => 'Something went wrong',
        ],
    ],

    'status' => [
        'active' => 'Active',
        'rotated' => 'Rotated',
        'deprecated' => 'Deprecated',
    ],

    'show' => [
        'page_title' => 'Account Details | IGI',
        'title_sub' => 'Account Data Dashboard',
        'pagetitle' => 'Account Details',

        'header' => [
            'title' => 'Account #:id - :type',
            'asset_line' => 'Asset: :code / :name',
        ],

        'sections' => [
            'metadata' => 'Metadata',
            'endpoint' => 'Endpoint',
            'credentials' => 'Credentials',
            'pending_approvals' => 'Pending Approvals',
            'notes' => 'Notes',
            'audit_logs' => 'Audit Log (latest 50)',
        ],

        'empty' => [
            'endpoint' => 'No endpoint yet.',
            'credentials' => 'No credentials yet.',
            'pending_approvals' => 'No approval requests.',
            'audit_logs' => 'No audit log for this account yet.',
        ],

        'labels' => [
            'asset' => 'Asset',
            'category' => 'Category',
            'status' => 'Status',
            'plant_site' => 'Plant/Site',
            'location' => 'Location',
            'area_location' => 'Area Location',
            'environment' => 'Environment',
            'criticality' => 'Criticality',
            'vendor_installer' => 'Vendor/Installer',
            'last_verified_at' => 'Last Verified',
            'verification_note_optional' => 'Verification note (optional)',
            'port' => 'Port',
            'username' => 'Username',
            'secret' => 'Secret',
        ],

        'actions' => [
            'verify' => 'Verify',
        ],

        'approvals' => [
            'requester' => 'Requester',
            'secret' => 'Secret',
            'created_at' => 'Created at',
            'reason' => 'Reason',
            'approve' => 'Approve',
        ],

        'secrets' => [
            'masked_note' => 'Password/secret is always masked. Revealing a secret requires re-auth, and (non Super Admin) needs approval.',
            'add_credential' => 'Add User/Credential',
            'add_credential_hint' => 'Does not rotate and does not disable other users. Useful for CCTV with multiple users.',
            'add_row' => 'Add Row',
            'bulk_paste_label' => 'Bulk Paste (optional)',
            'bulk_placeholder' => "Format per line: username|password|role (role optional)\nExample:\nadmin|P@ssw0rd|admin\noperator|P@ssw0rd|viewer",
            'parse' => 'Parse',
            'add_reason_optional' => 'Reason (optional)',
            'save_credentials' => 'Save Credentials',
            'inactive' => 'Inactive',
            'copy_username' => 'Copy Username',
            'reveal' => 'Reveal',
            'deactivate' => 'Deactivate',
            'rotate_title' => 'Rotate Secret',
            'kind_current' => 'current',
            'kind_default' => 'default',
            'label_optional' => 'label (optional)',
            'username_optional' => 'username (optional)',
            'new_secret' => 'new secret',
            'rotate' => 'Rotate',
            'reason_optional' => 'rotation reason (optional)',
            'notes' => 'Notes',

            'row' => [
                'role_label' => 'Role/Label',
                'role_placeholder' => 'admin/viewer/operator',
                'username' => 'Username',
                'username_placeholder' => 'admin',
                'password' => 'Password',
                'password_required' => 'Required',
                'remove' => 'Remove',
            ],
        ],

        'reauth' => [
            'modal_title' => 'Reveal Secret',
            'title' => 'Re-auth',
            'subtitle' => 'Confirm password for re-auth.',
            'password_confirm' => 'Confirm password',
            'result_label' => 'Result',
            'result_empty' => '(empty)',
            'audit_note' => 'Reveal/copy actions are logged to the audit log.',
        ],

        'swal' => [
            'copied' => 'Copied',
            'copy_failed' => 'Copy failed',
            'no_access' => 'No access.',

            'deactivate_title' => 'Deactivate credential?',
            'deactivate_text' => 'The credential will be deactivated and cannot be revealed again. This action is logged in the audit log.',
            'deactivate_yes' => 'Yes, deactivate',
            'sent' => 'Sent',
            'approval_sent' => 'Approval request sent successfully.',
            'approval_failed' => 'Failed to submit approval request.',
            'reveal_failed' => 'Reveal failed',

            'generic_error' => 'Something went wrong',

            'need_approval_title' => 'Approval required',
            'need_approval_confirm' => 'Request Approval',
            'need_approval_input_label' => 'Request reason (optional)',
            'need_approval_placeholder' => 'Example: troubleshooting CCTV gate A',
        ],

        'endpoint' => [
            'open' => 'Open',
            'open_local' => 'Open Local IP',
            'open_public' => 'Open Public IP',
            'toggle_dropdown' => 'Toggle Dropdown',
        ],

        'audit' => [
            'time' => 'Time',
            'actor' => 'Actor',
            'action' => 'Action',
            'result' => 'Result',
            'target' => 'Target',
            'reason' => 'Reason',
        ],
    ],
];
