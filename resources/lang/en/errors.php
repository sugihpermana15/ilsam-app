<?php

return [
    'network_error' => 'Network error. Please try again.',
    'required' => 'This field is required.',

    'titles' => [
        '401' => 'Ilsam - Unauthorized',
        '404' => 'Ilsam - Page Not Found',
        '500' => 'Ilsam - Server Error',
    ],

    'pages' => [
        'common' => [
            'auth_bg_alt' => 'Auth Background',
            'vector_alt' => 'Vector Image',
        ],
        'actions' => [
            'back_to_home' => 'Back to Home',
        ],
        '401' => [
            'headline_prefix' => 'Uh-oh,',
            'headline' => "you're not logged in!",
            'desc' => "It looks like you don't have permission to view this page. Please log in to proceed.",
        ],
        '404' => [
            'headline_prefix' => 'Oops!',
            'headline' => 'Page not found',
            'desc' => 'The URL you accessed is not available or has been moved.',
        ],
        '500' => [
            'headline_prefix' => 'Uh-oh,',
            'headline' => 'something broke!',
            'desc' => "Looks like we've encountered an issue. Our team is on it! Please try again in a few moments.",
        ],
    ],
];
