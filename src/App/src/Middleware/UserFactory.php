<?php

declare(strict_types = 1);

namespace App\Middleware;

use Mezzio\Handler\NotFoundHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class UserFactory
{
    public function __invoke(ContainerInterface $container) : MiddlewareInterface
    {
        $handler = $container->get(NotFoundHandler::class);

        return new UserMiddleware($handler);
    }
}
