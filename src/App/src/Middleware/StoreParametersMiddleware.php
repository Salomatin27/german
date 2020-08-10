<?php
declare(strict_types=1);

namespace App\Middleware;

use Mezzio\Router\RouteResult;
use Mezzio\Session\Session;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StoreParametersMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Session $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        /** @var RouteResult $route */
        $route = $request->getAttribute(RouteResult::class);
        $route_name = $route->getMatchedRouteName();
        $parameters = $request->getQueryParams();
        if (empty($parameters) && $session->has($route_name)) {
            $stored = $session->get($route_name);
            $request = $request->withQueryParams($stored);
        } elseif (!empty($parameters)) {
            $session->set($route_name, $parameters);
        }

        return $handler->handle($request);
    }

}