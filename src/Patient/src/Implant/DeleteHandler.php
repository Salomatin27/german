<?php
declare(strict_types=1);

namespace Patient\Implant;

use App\Service\Site;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
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
    private $site;

    public function __construct(PatientService $service, Site $site)
    {
        $this->service = $service;
        $this->site = $site;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = $request->getAttribute(UserInterface::class, null);

        $operation_implant_id = $request->getAttribute('id');
        $entity = $this->service->getOperationImplant($operation_implant_id);
        if (!$entity) {
            return new JsonResponse(['url' => '/404']);
        }

        $url = $this->site->getUrl();
        $url .= '/patient/' . $entity->getOperation()->getPatient()->getPatientId() . '#operation_implants_'
            . $entity->getOperation()->getOperationId();

        $result = $this->service->removeOperationImplant($entity, $identity);

        if ($result->error) {
            //return new JsonResponse(['url' => '/operation-implant/' . $operation_implant_id]);
            return new JsonResponse(['error' => $result->error]);
        }

        return new JsonResponse(['url' => $url]);
    }
}
