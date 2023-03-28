<?php

return [
    'failed' => 'Login failure! Please check your username and password.',
    'success' => 'Registration success!',
    'throttle' => 'Login failure multiple times. Wait :seconds then retry!',
    'ipd' => [
        'login' => [
            'success' => ''
        ],
        'register' => [
            '200' => 'Registration success',
            '400' => 'Account was taken. Please choose another username',
            '500' => 'Registration error. Please check your username & password! (Length must be at least 6)'
        ]
    ]
];
