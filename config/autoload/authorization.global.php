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
                'image.view',
                'patient.patch',
            ],
            'editor' => [
                'patients',
                'patient.edit',
                'patient.delete',
                'patient.create',
                'patient.save',
                'patient.code',
                'patient-images.list',
                'patient-images.save',
                'image.delete',
                'operation.create',
                'operation.delete',
                'reference.open',
                'reference.save',
                'reference.options',
                'reference.delete',
                'patient-photo.save',
                'patient-photo.get',
                'operation-implant.create',
                'operation-implant.edit',
                'operation-implant.delete',

            ],
            'administrator' => [
                'doctrine.test',
                'api.ping',
                'users',
                'user.edit',
                'user.change-password',
                'user.reset-password',
                'user.set-password',
                'user.message',
            ],
        ],
    ],
];