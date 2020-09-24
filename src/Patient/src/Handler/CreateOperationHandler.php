<?php
declare(strict_types=1);

namespace Patient\Handler;

use App\Entity\Patient;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\TextResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateOperationHandler implements RequestHandlerInterface
{
    private $service;
    private $template;
    private $code;
    private $site;

    public function __construct(
        TemplateRendererInterface $template,
        PatientService $service
    ) {

        $this->service = $service;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = $request->getAttribute(UserInterface::class, null);
        $patient_id = $request->getAttribute('id', false);
        /** @var Patient $patient */
        $patient = $this->service->getPatient($patient_id);
        if (!$patient) {
            return new RedirectResponse('/404');
        }

        $result = $this->service->createOperation($patient, $identity);

        if ($result->error) {
            // for logged
            return new TextResponse('error');
        }

        $operation = $result->operation;
        $surgeons = $this->service->getAllSurgeons();
        $clinics = $this->service->getAllClinics();
        $kinds = $this->service->getAllKinds();

        return new HtmlResponse($this->template->render('patient::row-operation', [
            'operation'  => $operation,
            'identity'   => $identity,
            'layout'     => false,
            'surgeons'   => $surgeons,
            'clinics'    => $clinics,
            'kinds'      => $kinds,
            'object_num' => 'new',
        ]));
    }
}
