<?php
declare(strict_types=1);

namespace User\Action;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Service\UserService;

class CreateUserAction implements RequestHandlerInterface
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
        $identity = $request->getAttribute(UserInterface::class, null);

        /** @var FlashMessages $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $flashMessages->clearFlash();
        $result = $this->userService->createPatient($identity);
        /** @var User $user */
        $user = $result->user;
        if ($result->error) {
            $flashMessages->flash('warning', $result->error);
            return new RedirectResponse('/patients');
        }
        $flashMessages->flash('success', 'Patient created');
        return new RedirectResponse('/user/' . $user->getUserId());
    }

}