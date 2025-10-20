<?php

return [

    'auth' => [
        'guard' => 'web',
        'pages' => [
            'login' => \Filament\Http\Livewire\Auth\Login::class,
        ],
    ],

    'panels' => [
        App\Providers\Filament\AdminPanelProvider::class,
    ],

    'broadcasting' => [
        // ...
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
    'assets_path' => null,
    'cache_path' => base_path('bootstrap/cache/filament'),
    'livewire_loading_delay' => 'default',
    'system_route_prefix' => 'filament',
];
