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
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Doctrine',
                'route' => 'doctrine.test',
            ],
            [
                'label' => 'Logout',
                'route' => 'logout',
                'title' => '<i class="fa fa-sign-out-alt"></i>',
            ],
        ],
    ],
];
