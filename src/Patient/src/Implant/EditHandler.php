<?php
declare(strict_types=1);

namespace Patient\Implant;

use App\Entity\OperationImplant;
use App\Service\Site;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\PatientService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\CodeService;

class EditHandler implements RequestHandlerInterface
{
    private $service;
    private $template;
    private $code;
    private $site;

    public function __construct(
        TemplateRendererInterface $template,
        PatientService $service,
        CodeService $code,
        Site $site
    ) {

        $this->service = $service;
        $this->template = $template;
        $this->code = $code;
        $this->site = $site;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = $request->getAttribute(UserInterface::class, null);
        $operation_implant_id = $request->getAttribute('id', false);
        /** @var OperationImplant $entity */
        $entity = $this->service->getOperationImplant($operation_implant_id);
        if (!$entity) {
            return new RedirectResponse('/404');
        }
        /** @var SessionCsrfGuard $guard */
//        $guard     = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        /** @var FlashMessages $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $flashMessages->clearFlash();


        $prg = $request->getParsedBody();
        $load_current = false;

        if ($prg) {
                $result = $this->service->setOperationImplant($entity, $prg, $identity);

            if ($result->error) {
                $this->flashNow('warning', $result->error);
                $load_current = true;
            } else {
                // не нужен, так как переход на нужную операцию
                //$flashMessages->flashNow('success', 'The operation implants are saved');
                $url = $this->site->getUrl();
                $url .= '/patient/' . $entity->getOperation()->getPatient()->getPatientId() . '#operation_implants_'
                . $entity->getOperation()->getOperationId();
                return new RedirectResponse($url);
            }
        }

        if ($load_current) {
            $manufacturers = $prg['manufacturers'] ?? null;
            $implants = $prg['implants'] ?? null;
            $fixations = $prg['fixations'] ?? null;
        } else {
            $manufacturer = $entity->getImplant() ? $entity->getImplant()->getManufacturer() : null;
            $kind = $entity->getOperation()->getKind();
            $manufacturers = $this->service->getAllManufacturers();
            $implants = $this->service->getImplantsByManufacturerKind($manufacturer, $kind);
            $fixations = $this->service->getFixationsByManufacturerKind($manufacturer, $kind);
        }

//        $token   = $guard->generateToken('operation_implant_' . $operation_implant_id);
//        $form->get('csrf')->setValue($token);



        return new HtmlResponse($this->template->render(
            'implant::edit',
            [
                'item'        => $entity,
                'identity'      => $identity,
                'manufacturers' => $manufacturers,
                'implants'      => $implants,
                'fixations'     => $fixations,
            ]
        ));
    }

    private function flashNow($key, $message)
    {
        $_SESSION['Mezzio\Flash\FlashMessagesInterface::FLASH_NEXT'] = [
            $key => [
                'value' => $message,
                'hops'  => 1
            ]
        ];
    }
}
