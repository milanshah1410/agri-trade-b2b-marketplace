<?php

return [

    /*
    | First Super Admin, created by SuperAdminSeeder. Read via config() (not env()) so it keeps
    | working when configuration is cached in production.
    */
    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
        'email' => env('SUPER_ADMIN_EMAIL'),
        'password' => env('SUPER_ADMIN_PASSWORD'),
    ],

];
