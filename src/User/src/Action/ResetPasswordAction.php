<?php

namespace User\Action;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Form\ResetPasswordForm;
use User\Service\UserService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class ResetPasswordAction implements RequestHandlerInterface
{
    private $userService;
    private $template;

    public function __construct(UserService $userService, TemplateRendererInterface $templateRenderer)
    {
        $this->userService = $userService;
        $this->template = $templateRenderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new ResetPasswordForm();

        if ($request->getMethod()=='POST') {
            $data = $request->getParsedBody();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->userService->findUserByEmail($data['email']);
                if ($user!=null) {
                    // Generate a new password for user and send an E-mail
                    // notification about that.
                    $this->userService->generatePasswordResetToken($user);

                    // Redirect to "message" page
                    return new RedirectResponse('/user/message/sent');
                } else {
                    return new RedirectResponse('/user/message/invalid-email');
                }
            }
        }
        return new HtmlResponse($this->template
            ->render(
                'user::reset-password',
                [
                    'form' => $form,
                    'layout' => 'layout::empty'
                ]
            ));
    }
}
