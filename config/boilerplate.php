<?php

return [

    'sign_up' => [
        'release_token' => env('SIGN_UP_RELEASE_TOKEN'),
        'validation_rules' => [
            'name' => 'required',
            // 'email' => 'required',
            'password' => 'required'
        ]
    ],

    'login' => [
        'validation_rules' => [
            'name' => 'required',
            'password' => 'required'
        ]
    ],

    'forgot_password' => [
        'validation_rules' => [
            'name' => 'required'
        ]
    ],

    'reset_password' => [
        'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),
        'validation_rules' => [
            'token' => 'required',
            // 'email' => 'required',
            'name' => 'required',

            'password' => 'required|confirmed'
        ]
    ]

];
