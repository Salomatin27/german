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
        $manufacturer = $kind = null;
        $item = $request->getAttribute('item', null);
        if ($item === null) {
            return new HtmlResponse($this->template->render('error::404'));
        }

        $parameters = $request->getQueryParams();
        $manufacturer_id = $parameters['manufacturer'] ?? null;
        $kind_id = $parameters['kind'] ?? null;
        //$manufacturer_id = $request->getAttribute('manufacturer_id', null);
        if ($item === 'fixation' || $item === 'implant') {
            try {
                $manufacturer = $this->service->getManufacturerById($manufacturer_id);
                $kind = $this->service->getKindById($kind_id);
            } catch (\Exception $exception) {
                return new HtmlResponse($this->template->render('reference::not-found-manufacturer', [
                    'layout' => false,
                ]));
            }
        }

        $data = $this->service->getReference($item, $manufacturer, $kind);
        $html = 'reference::ref-'.$item;
        return new HtmlResponse($this->template->render(
            $html,
            [
                'items'  => $data,
                'layout' => false,
                'manufacturer' => $manufacturer,
            ]
        ));
    }
}
