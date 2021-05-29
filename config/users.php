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

    /*
    |--------------------------------------------------------------------------
    | Pronouns
    |--------------------------------------------------------------------------
    |
    | What pronouns do we allow?
    |
    */

    'pronouns' => [
      'masculine' => [
        'subjective' => 'he',
        'objective'  => 'him',
        'possessive' => 'his'
      ],
      'feminine' => [
        'subjective' => 'she',
        'objective'  => 'her',
        'possessive' => 'her'
      ],
      'neuter' => [
        'subjective' => 'it',
        'objective'  => 'it',
        'possessive' => 'its'
      ],
      'plural' => [
        'subjective' => 'they',
        'objective'  => 'them',
        'possessive' => 'their'
      ],
    ],
];
