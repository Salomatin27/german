<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\TextResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeleteHandler implements RequestHandlerInterface
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

        $patient_id = $request->getAttribute('id');

        $result = $this->service->removePatient($patient_id, $identity);

        if ($result->error) {
            $flashMessages->flash('warning', $result->error);
            //return new RedirectResponse('/patient/edit' . $patient_id);
        } else {
            $message = 'Patient ' . '<b>' . $result->name . '</b>' . ' removed';
            $message .= ' / Patient ' . '<b>' . $result->name . '</b>' . ' entfernt';
            $flashMessages->flash('success', $message);
        }

        return new JsonResponse(['url' => '/patients']);
    }

}