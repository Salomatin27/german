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
use User\Form\UserForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class EditUserAction implements RequestHandlerInterface
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
        if ($id === '0') {
            $user = new User();
            $action = 'create';
        } else {
            $user = $this->entityManager->getRepository(User::class)->find($id);
            $action = 'update';
        }

        $form = new UserForm($this->entityManager, $user, $guard, $action);
        $prg = $request->getParsedBody();
        if ($prg) {
            $data = $prg;
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($id === '0') {
                    $result = $this->userService->addUser($data);
                } else {
                    $result = $this->userService->updateUser($user, $data);
                }
                if ($result->error) {
                    $this->flashNow('warning', $result->error);
                } else {
                    $flashMessages->flashNow('success', 'User '.$result->user->getFullName().' saved');
                    return new RedirectResponse('/users');
                }
            }

            foreach ($form->getMessages() as $element => $messages) {
                foreach ($messages as $key => $message) {
                    //$flashMessages->flashNow('warning', $element.' > '.$message);
                    $this->flashNow('warning', $element.' > '.$message);
                    break;
                }
                break;
            }

            $form->setData($data);
        }


        $token   = $guard->generateToken('user_' . $id);
        $form->get('csrf')->setValue($token);

        return new HtmlResponse($this->template
            ->render(
                'user::edit-user',
                [
                    'user'   => $user,
                    'form'   => $form,
                    'action' => $action
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
