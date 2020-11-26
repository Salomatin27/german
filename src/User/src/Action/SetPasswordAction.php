<?php

namespace User\Action;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Form\ChangePasswordForm;
use User\Service\UserService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class SetPasswordAction implements RequestHandlerInterface
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
        $query = $request->getQueryParams();
        $token = $query['token'];

        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }

        if ($token===null ||
            !$this->userService->validatePasswordResetToken($token)) {
            return new RedirectResponse('/user/message/failed');
        }

        // Create form
        $form = new ChangePasswordForm('reset');

        // Check if user has submitted the form
        if ($request->getMethod()=='POST') {
            $data = $request->getParsedBody();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                // Set new password for the user.
                if ($this->userService->setNewPasswordByToken($token, $data['new_password'])) {
                    // Redirect to "message" page
                    return new RedirectResponse('/user/message/set');
                } else {
                    // Redirect to "message" page
                    return new RedirectResponse('/user/message/failed');
                }
            }
        }

        return new HtmlResponse($this->template->render('user::set-password', [
            'form' => $form,
            'layout' => 'layout::empty'

        ]));
    }
}
