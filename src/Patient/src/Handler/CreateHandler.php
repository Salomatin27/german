<?php
declare(strict_types=1);

namespace Patient\Handler;

use App\Entity\Patient;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateHandler implements RequestHandlerInterface
{
    private $service;
    public function __construct(PatientService $service)
    {
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = $request->getAttribute(UserInterface::class, null);

        /** @var FlashMessages $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $flashMessages->clearFlash();
        $result = $this->service->createPatient($identity);
        /** @var Patient $patient */
        $patient = $result->patient;
        if ($result->error) {
            $flashMessages->flash('warning', $result->error);
            return new RedirectResponse('/patients');
        }
        $flashMessages->flash('success', 'Patient created');
        return new RedirectResponse('/patient/' . $patient->getPatientId());
    }

}