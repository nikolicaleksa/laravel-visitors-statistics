<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tracking conditions
    |--------------------------------------------------------------------------
    */

    'track_authenticated_users' => false,

    'track_ajax_request' => false,

    /*
    |--------------------------------------------------------------------------
    | Login attempts
    |--------------------------------------------------------------------------
    |
    | Login attempts should not be tracked as visits
    | If you want to track them set the value to false
    |
    */

    'login_route_path' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    |
    | Specifcy prefix and middleware that should be used
    | when registering routes for the package
    |
    */

    'prefix' => 'admin',

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Maxmind database
    |--------------------------------------------------------------------------
    */

    'database_location' => storage_path('app/maxmind.mmdb'),

    'database_download_url' => 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',

    'auto_update' => true,
];
