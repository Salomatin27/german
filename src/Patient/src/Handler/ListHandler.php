<?php

declare(strict_types=1);

namespace Patient\Handler;

use Mezzio\Authentication\UserInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class ListHandler implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;
    /**
     * @var PatientService
     */
    private $service;

    public function __construct(TemplateRendererInterface $renderer, PatientService $service)
    {
        $this->renderer = $renderer;
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $page = $request->getAttribute('page', false);
        $identity = $request->getAttribute(UserInterface::class, null);
        $para = $request->getQueryParams();

        if ($page !== false) {
            $items = $this->service->getPatients($para, $page);
            return new HtmlResponse($this->renderer->render('patient::list-parts', [
                'items'  => $items,
                'layout'   => false,
            ]));
        }

        return new HtmlResponse($this->renderer->render(
            'patient::list',
            [
                'identity'=> $identity,
                'parameters' => $para,
                'keyword' => $para['keyword'] ?? null,
            ] // parameters to pass to template
        ));
    }
}
