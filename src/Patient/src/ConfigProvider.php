<?php

declare(strict_types=1);

namespace Patient;

/**
 * The configuration provider for the Patient module
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
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
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
                'patient'      => [__DIR__ . '/../templates/'],
                'reference'    => [__DIR__ . '/../templates/reference/'],
                'implant'      => [__DIR__ . '/../templates/implant/'],
            ],
        ];
    }
}
