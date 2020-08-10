<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Navigation\ExpressiveNavigationPage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RecursiveIteratorIterator;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Mezzio\Session\SessionMiddleware;
use Laminas\Navigation\AbstractContainer;
use Laminas\Permissions\Rbac\Rbac;

class NavigationMiddleware implements MiddlewareInterface
{
    /**
     * @var AbstractContainer[]
     */
    private $containers = [];
    /**
     * @var Rbac
     */
    private $rbac;

    /**
     * @param AbstractContainer[] $containers
     * @param Rbac $rbac
     */
    public function __construct(array $containers, Rbac $rbac)
    {
        foreach ($containers as $container) {
            if (! $container instanceof AbstractContainer) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid argument: container must be an instance of %s',
                    AbstractContainer::class
                ));
            }

            $this->containers[] = $container;
        }
        $this->rbac = $rbac;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $user = $session->get(UserInterface::class);

        $routeResult = $request->getAttribute(RouteResult::class, false);

        if (! $routeResult instanceof RouteResult) {
            return $handler->handle($request);
        }

        foreach ($this->containers as $container) {
            $iterator = new RecursiveIteratorIterator(
                $container,
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $page) {
                if ($page instanceof ExpressiveNavigationPage) {
                    $page->setRouteResult($routeResult);
                    if (!$user || !$this->rbac->isGranted($user['roles'][0], $page->getRoute())) {
                        $page->setVisible(false);
                    }
                }
            }
        }

        return $handler->handle($request);
    }

}