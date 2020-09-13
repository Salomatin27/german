<?php

declare(strict_types = 1);

namespace App\Handler;

use App\Form\LoginForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginPageHandler implements MiddlewareInterface
{
    private $template;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->template = $templateRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParameters = $request->getQueryParams();
        $redirectUrl = isset($queryParameters['redirectUrl'])
            ? $queryParameters['redirectUrl'] : '/';

//        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
//        if ($session->has(UserInterface::class)) {
//            return new RedirectResponse($redirectUrl);
//        }
//
//
        /** @var SessionCsrfGuard $guard */
        $guard     = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $loginForm = new LoginForm($guard);
        $loginForm->get('redirect_url')->setValue($redirectUrl);

        $prg = $request->getParsedBody();

        if ($prg) {
            $loginForm->setData($prg);
            if ($loginForm->isValid()) {
                $response = $handler->handle($request);

                /** @var FlashMessages $flashMessages */
                $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
                if ($response->getStatusCode() !== 302) {
                    $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
                    $user = $session->get(UserInterface::class);
                    $message = 'Hello ' . $user['details']['user_name'];
                    $flashMessages->flash('info', $message);
                    return new RedirectResponse($redirectUrl);
                }
                $flashMessages->flash('warning', 'Invalid login details');
                //return new RedirectResponse('/login');
                return $response;
            }
        }

        //$session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $token   = $guard->generateToken();
        return new HtmlResponse($this->template->render('app::login-page', [
            'login_form' => $loginForm,
            'token'      => $token,
            'layout'     => 'layout::empty',
        ]));
    }

}