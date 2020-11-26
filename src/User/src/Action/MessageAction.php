<?php

namespace User\Action;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Server\RequestHandlerInterface;

class MessageAction implements RequestHandlerInterface
{
    private $template;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->template = $templateRenderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', 'none');

        // Validate input argument.
        if ($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }

        return new HtmlResponse($this->template->render(
            'user::message',
            [
                'id' => $id,
                'layout' => 'layout::empty'
            ]
        ));
    }
}
