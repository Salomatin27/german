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
        $parameters = $request->getQueryParams();
        $manufacturer_id = $parameters['manufacturer'] ?? null;
        $kind_id = $parameters['kind'] ?? null;
        if ($item === null) {
            return new HtmlResponse($this->template->render('error::404'));
        }
        $manufacturer = $kind = null;
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