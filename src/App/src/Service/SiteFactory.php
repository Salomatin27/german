<?php

namespace App\Service;

use Psr\Container\ContainerInterface;

class SiteFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Site($container);
    }
}