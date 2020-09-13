<?php
declare(strict_types=1);

namespace Patient\Reference;

use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class OpenHandler implements RequestHandlerInterface
{
    private $template;
    private $service;

    public function __construct(TemplateRendererInterface $template, PatientService $service)
    {
        $this->service = $service;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $item = $request->getAttribute('item', null);
        if ($item === null) {
            return new HtmlResponse($this->template->render('error::404'));
        }

        $data = $this->service->getReference($item);
        $html = 'reference::ref-'.$item;
        return new HtmlResponse($this->template->render(
            $html,
            [
                'items'  => $data,
                'layout' => false,
            ]
        ));
    }

}