<?php

return [
  'antivirus' => [
    // Requires ClamAV (clamscan) installed on the server.
    // Enable with CAREER_AV_ENABLED=true
    'enabled' => (bool) env('CAREER_AV_ENABLED', false),

    // Command name or full path.
    // Examples: clamscan | /usr/bin/clamscan | C:\\Program Files\\ClamAV\\clamscan.exe
    'command' => (string) env('CAREER_AV_COMMAND', 'clamscan'),

    // If true: when scanner errors/unavailable -> reject upload.
    'fail_closed' => (bool) env('CAREER_AV_FAIL_CLOSED', true),
  ],

  'recaptcha' => [
    // Enable with RECAPTCHA_ENABLED=true and set keys in services.php env.
    'enabled' => (bool) env('RECAPTCHA_ENABLED', false),
  ],
];
