<?php

declare(strict_types=1);

namespace App;

use App\Factory\AbstractHandlerFactory;
use App\Helper\Flash;
use App\Helper\LngButton;
use App\Helper\LngLabel;
use App\Helper\LngLabelFactory;
use App\Middleware\PrgMiddleware;
use App\Middleware\StoreParametersMiddleware;
use App\Service\Site;
use App\Service\SiteFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'view_helpers' => [
                'invokables' => [
                    'flash' => Flash::class,
                    'lngLabel' => LngLabel::class,
                    'lngButton' => LngButton::class,
                ],
                'aliases' => [

                ],
                'factories' => [
                ]
            ],
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories'  => [
                Handler\HomePageHandler::class => Handler\HomePageHandlerFactory::class,
                PrgMiddleware::class => InvokableFactory::class,
                StoreParametersMiddleware::class => InvokableFactory::class,
                Site::class => SiteFactory::class,
            ],
            'abstract_factories' => [
                AbstractHandlerFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
                'partial'=> [__DIR__ . '/../templates/partial'],
            ],
        ];
    }
}
