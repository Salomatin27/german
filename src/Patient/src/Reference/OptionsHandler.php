<?php
declare(strict_types=1);

namespace Patient\Reference;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OptionsHandler implements RequestHandlerInterface
{
    private $service;
    private $template;

    public function __construct(TemplateRendererInterface $template, PatientService $service)
    {

        $this->template = $template;
        $this->service = $service;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $item = $request->getAttribute('item', null);
        if ($item === null) {
            return new HtmlResponse($this->template->render('error::404'));
        }

        $data = $this->service->getReference($item);
        $html = 'reference::ref-'.$item.'-options';
        return new HtmlResponse($this->template->render(
            $html,
            [
                'items'=>$data,
                'layout'=>false,
            ]
        ));
    }

}