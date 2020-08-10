<?php

declare(strict_types = 1);

namespace App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\DefaultUser;
use Mezzio\Authentication\UserInterface;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserMiddleware implements MiddlewareInterface
{
    private $notFoundMiddleware;

    public function __construct(NotFoundHandler $notFoundHandler)
    {
        $this->notFoundMiddleware = $notFoundHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $redirectUrl = $request->getRequestTarget();
        $action = mb_substr($redirectUrl, 1, 5);
        if ($action !== 'login' && strlen($redirectUrl)>2048) {
            $redirectUrl = '/';
        }

        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        // no session
        // - set roles as "guest"
        // - when status code !== 403 or page = /login, return response
        // - otherwise, redirect to login page
        if (! $session->has(UserInterface::class)) {
            $user = '';
            $roles = ['guest'];

            $request = $request->withAttribute(
                UserInterface::class,
                new DefaultUser(
                    $user,
                    $roles
                )
            );

            $response = $handler->handle($request);
            if ($request->getUri()->getPath() === '/login' ||
                $response->getStatusCode() !== 403
            ) {
                return $response;
            }

            return new RedirectResponse('/login?redirectUrl=' . $redirectUrl);
        }

        // has session but at /login page, redirect to authenticated page
        if ($request->getUri()->getPath() === '/login') {
            return new RedirectResponse('/');
        }

        // define roles from DB
        $sessionData = $session->get(UserInterface::class);
        $request = $request->withAttribute(
            UserInterface::class,
            new DefaultUser(
                $sessionData['username'],
                $sessionData['roles'],
                $sessionData['details']
            )
        );
        return $handler->handle($request);
    }
}
