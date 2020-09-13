<?php

namespace App\Service;

use Psr\Container\ContainerInterface;

class Site
{
    const CONFIG_KEY = 'site';
    const SITE_DEV = 'http://localhost:8017';
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getUrl()
    {
        if (! $this->container->has('config')) {
            return self::SITE_DEV;
        }

        $config = $this->container->get('config');
        if (! isset($config[self::CONFIG_KEY])
            || ! is_array($config[self::CONFIG_KEY])
        ) {
            return self::SITE_DEV;
        }

        $arrays = $config[self::CONFIG_KEY];

        if (! isset($arrays['url'])) {
            return self::SITE_DEV;
        }

        return $arrays['url'];
    }

    public function getName()
    {
        if (! $this->container->has('config')) {
            return '';
        }

        $config = $this->container->get('config');
        if (! isset($config[self::CONFIG_KEY])
            || ! is_array($config[self::CONFIG_KEY])
        ) {
            return '';
        }

        $arrays = $config[self::CONFIG_KEY];

        return $arrays['name'];
    }
}
