<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message Types
    |--------------------------------------------------------------------------
    |
    | The different kinds of message types referenced in the messages partial.
    | Use them when returning a view or a redirect using the "with" helper.
    | As in: return view('users.index')->with('output', $output)->with('status', $status);.
    |
    */

    'message_types' => [
        'success',
        'error',
        'status',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models & Actions
    |--------------------------------------------------------------------------
    |
    | The the models and actions for use in loggers search.
    |
    */

    'models' => [
        'User',
    ],

    'actions' => [
        'Create',
        'Update',
        'Delete',
    ],

];
