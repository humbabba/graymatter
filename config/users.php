<?php

return [

    /*
    |--------------------------------------------------------------------------
    | New users
    |--------------------------------------------------------------------------
    |
    | Do we allow the public to register? Do we allow admins to create users? Or both?
    |
    */

    'new' => [
      'register' => env('USERS_REGISTER', true),
      'create' => env('USERS_CREATE', true),
    ],

];
