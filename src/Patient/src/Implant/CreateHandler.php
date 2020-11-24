<?php
declare(strict_types=1);

namespace Patient\Implant;

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
        $operation_id = $request->getAttribute('id', null);

        /** @var FlashMessages $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $flashMessages->clearFlash();
        $result = $this->service->createOperationImplant($identity, $operation_id);
        if ($result->error) {
            $flashMessages->flash('warning', $result->error);
            return new RedirectResponse($result->redirect);
        }
        $flashMessages->flash('success', 'Implant created');
        return new RedirectResponse('/operation-implant/' . $result->operation_implant_id);
    }

}