<?php

use App\Navigation\ExpressiveNavigationAbstractServiceFactory;
use App\Navigation\ExpressiveNavigationFactory;
use Laminas\Navigation\Navigation;
use Laminas\Navigation\View\ViewHelperManagerDelegatorFactory;
use Laminas\View\HelperPluginManager;

return [
    'dependencies' => [
        'factories'  => [
            Navigation::class =>
                ExpressiveNavigationFactory::class,
            App\Middleware\NavigationMiddleware::class =>
                App\Middleware\NavigationFactory::class,
        ],
        'delegators' => [
            HelperPluginManager::class => [
                ViewHelperManagerDelegatorFactory::class,
            ],
        ],
        'aliases' => [
            'navigation' => Navigation::class,
        ],
        'abstract_factories' => [
            ExpressiveNavigationAbstractServiceFactory::class,
        ]
    ],

    'navigation' => [
        'default' => [
            [
                'label' => 'Haus',
                'route' => 'home',
                'english' => 'home'
            ],
            [
                'label' => 'Patienten',
                'route' => 'patients',
                'english' => 'patients',
                'pages' => [
                    [
                        'label' => 'Patient',
                        'route' => 'patient.edit',
                    ],
                ],
            ],
            [
                'label' => 'Ausloggen',
                'route' => 'logout',
                'english' => 'logout',
                'title' => '<i class="fa fa-sign-out-alt"></i>',
            ],
        ],
    ],
];
