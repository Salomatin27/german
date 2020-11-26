<?php

namespace User\Action;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Service\UserService;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class ListUsersAction implements RequestHandlerInterface
{
    private $userService;
    private $template;

    public function __construct(
        UserService $userService,
        TemplateRendererInterface $templateRenderer
    ) {

        $this->userService = $userService;
        $this->template = $templateRenderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $users = $this->userService->getUsers();
        return new HtmlResponse($this->template->render('user::list-users', ['users'=>$users]));
    }
}
