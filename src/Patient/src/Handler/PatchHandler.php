<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Authentication\UserInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PatchHandler implements RequestHandlerInterface
{
    /**
     * @var PatientService
     */
    private $service;

    public function __construct(PatientService $service)
    {

        $this->service = $service;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = $request->getAttribute(UserInterface::class, null);
        $data = $request->getParsedBody();
        $id = $request->getAttribute('id', '0');
        $result = $this->service->patchPatient($id, $data, $identity);

        return new JsonResponse($result);
    }
}
