<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response\TextResponse;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeleteOperationHandler implements RequestHandlerInterface
{
    private $service;

    public function __construct(PatientService $service)
    {
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', false);
        if (!$id) {
            return new TextResponse('not found');
        }
        $result = $this->service->removeOperation($id);

        return new TextResponse($result);
    }

}