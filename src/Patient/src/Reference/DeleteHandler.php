<?php
declare(strict_types=1);

namespace Patient\Reference;

use Laminas\Diactoros\Response\JsonResponse;
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

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id', null);
        $item = $request->getAttribute('item', null);
        $result = $this->service->removeReference($id, $item);

        return new JsonResponse([$result->error]);
    }
}