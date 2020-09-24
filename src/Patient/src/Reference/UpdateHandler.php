<?php
declare(strict_types=1);

namespace Patient\Reference;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UpdateHandler implements RequestHandlerInterface
{
    private $template;
    private $service;

    public function __construct(TemplateRendererInterface $template, PatientService $service)
    {

        $this->template = $template;
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ref = $request->getAttribute('item', null);
        $data = $request->getParsedBody();
        //$ref = $data['keyword'];
        if ($ref === 'surgeon') {
            $data = $this->service->setSurgeon($data);
            $html = 'reference::view-surgeon';
        } elseif ($ref === 'clinic') {
            $data = $this->service->setClinic($data);
            $html = 'reference::view-clinic';
        } elseif ($ref === 'kind') {
            $data = $this->service->setKind($data);
            $html = 'reference::view-kind';
        } else {
            return new EmptyResponse();
        }

        return new HtmlResponse($this->template->render($html, ['layout'=>false, 'item'=>$data]));
    }

}