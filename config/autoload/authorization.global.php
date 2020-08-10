<?php

return [
    'mezzio-authorization-rbac' => [
        'roles' => [
            'administrator' => [],
            'editor' => ['administrator'],
            'contributor' => ['editor'],
            'guest' => ['contributor'],
        ],
        'permissions' => [
            'guest' => [
                'login',
            ],
            'contributor' => [
                'home',
                'logout',
            ],
            'editor' => [

            ],
            'administrator' => [
                'doctrine.test',
                'api.ping',
            ],
        ],
    ],
];