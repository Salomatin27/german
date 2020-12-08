<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PrintPatientHandler implements RequestHandlerInterface
{
    private $template;
    private $service;

    public function __construct(TemplateRendererInterface $renderer, PatientService $service)
    {
        $this->template = $renderer;
        $this->service = $service;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', false);

        if (!$id) {
            return new RedirectResponse('error::404');
        }

        $output = $this->service->printPatient($id);

        return new HtmlResponse($this->template->render('patient::patient-print', [
            'html' => $output,
            'layout' => false,
        ]));
    }
}
