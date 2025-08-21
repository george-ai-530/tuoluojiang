<?php

declare(strict_types=1);
use App\Http\Model\Admin\Admin;
use App\Http\Model\User\User;
use App\Http\Model\Ldap\LdapUser;

return [
    'defaults' => [
        'guard' => 'admin',
    ],

    'guards' => [
        'admin' => [
            'driver' => 'ldap',
            'provider' => 'ldap',
            'remember' => true
        ],
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'user' => [
            'driver' => 'jwt',
            'provider' => 'user',
            'hash' => true,
        ],
        'ldap' => [
            'driver' => 'request',
            'provider' => 'ldap',
            'hash' => false
        ]
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
        'admin' => [
            'driver' => 'eloquent',
            'model' => Admin::class,
        ],
        'ldap' => [
            'driver' => 'custom',
            'model' => LdapUser::class,
        ],
        'custom' => [
            'driver' => 'custom',
            'model' => LdapUser::class
        ]
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    'ldap' => [
        'url' => env('LDAP_URL'),
        'bindDn' => env('LDAP_BIND_DN'),
        'bindPassword' => env('LDAP_BIND_PASSWORD'),
        'user' => [
            'baseDn' => env('LDAP_USER_BASE_DN'),
            'request' => env('LDAP_USER_REQUEST', 'uid'),
            'realNameAttribute' => env('LDAP_USER_REAL_NAME', 'cn'),
            'emailAttribute' => env('LDAP_USER_EMAIL', 'mail'),
        ],
    ],
];