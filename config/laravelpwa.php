<?php

return [
    'name' => env('APP_NAME', 'My PWA App'),
    'manifest' => [
        'name' => env('APP_NAME', 'My PWA App'),
        'short_name' => env('APP_NAME', 'PWA'),
        'start_url' => '/home',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [
            '256x256' => [
                'path' => 'android-chrome-256x256.png',
                'purpose' => 'any',
            ],
        ],

        'shortcuts' => [
            [
                'name' => 'Home',
                'description' => 'Go home',
                'url' => '/',
                'icons' => [
                    'src' => '/images/icons/icon-72x72.png',
                    'purpose' => 'any',
                ],
            ]
        ],
        'custom' => [],
        'splash' => [
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
    ],
];
