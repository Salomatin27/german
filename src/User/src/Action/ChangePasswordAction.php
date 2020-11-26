<?php

namespace User\Action;

use App\Entity\User;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Service\UserService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use User\Form\ChangePasswordForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class ChangePasswordAction implements RequestHandlerInterface
{
    private $userService;
    private $entityManager;
    private $template;

    public function __construct(
        EntityManager $entityManager,
        UserService $userService,
        TemplateRendererInterface $templateRenderer
    ) {

        $this->userService = $userService;
        $this->template = $templateRenderer;
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var FlashMessages $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $flashMessages->clearFlash();

        /** @var SessionCsrfGuard $guard */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);


        $id = $request->getAttribute('id', '0');
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if ($user === null) {
            return new HtmlResponse($this->template->render('error::404'));
        }

        $form = new ChangePasswordForm($this->entityManager, $guard, 'reset');
        $prg = $request->getParsedBody();
        if ($prg) {
            $data = $prg;
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $result = $this->userService->changePassword($user, $data);
                if (!$result) {
                    $message = 'Das aktuelle Passwort wurde falsch eingegeben. Das Passwort wurde nicht geändert.';
                    //$message = 'The current password was entered incorrectly. The password has not been changed.';
                    $this->flashNow('warning', $message);
                } else {
                    $message = 'Das Passwort wurde erfolgreich geändert.';
                    //$message = 'Password changed successfully.';
                    $flashMessages->FlashNow('success', $message);
                    return new RedirectResponse('/users');
                }

            }
        }

        $token   = $guard->generateToken('user_reset');
        $form->get('csrf')->setValue($token);

        return new HtmlResponse($this->template
            ->render(
                'user::change-password',
                [
                    'user' => $user,
                    'form' => $form
                ]
            ));
    }

    private function flashNow($key, $message)
    {
        $_SESSION['Mezzio\Flash\FlashMessagesInterface::FLASH_NEXT'] = [
            $key => [
                'value' => $message,
                'hops'  => 1
            ]
        ];
    }
}
