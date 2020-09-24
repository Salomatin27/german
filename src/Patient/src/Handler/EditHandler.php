<?php
declare(strict_types=1);

namespace Patient\Handler;

use App\Entity\Patient;
use App\Service\Site;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Csrf\SessionCsrfGuard;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessages;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Form\ImageForm;
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
        $patient_id = $request->getAttribute('id', false);
        /** @var Patient $patient */
        $patient = $this->service->getPatient($patient_id);
        if (!$patient) {
            return new RedirectResponse('/404');
        }
        /** @var SessionCsrfGuard $guard */
        $guard     = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        /** @var FlashMessages $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $flashMessages->clearFlash();

        $form = $this->service->initPatientForm($patient, $guard);

        if (!$form) {
            return new RedirectResponse('error::404');
        }

        $prg = $request->getParsedBody();
        $load_current = false;

        if ($prg) {
            $form->setData($prg);
            if ($form->isValid()) {
                $data = $form->getData();
                $data['operation'] = $prg['operation'] ?? null;
                $result = $this->service->setPatient($patient, $data, $identity);

                if ($result->error) {
                    //$flashMessages->flashNow('warning', $result->error);
                    $this->flashNow('warning', $result->error);
                    $load_current = true;
                } else {
                    //$flashMessages->flashNow('success', 'Patient '.$result->patient->getSurname().' saved');
                    $this->flashNow('success', 'Patient '.$result->patient->getSurname().' saved');
                    $form = $this->service->initPatientForm($result->patient, $guard);
                }
            } else {
                foreach ($form->getMessages() as $element => $messages) {
                    foreach ($messages as $key => $message) {
                        //$flashMessages->flashNow('warning', $element.' > '.$message);
                        $this->flashNow('warning', $element.' > '.$message);
                        break;
                    }
                    break;
                }
                $load_current = true;
            }
        }

        if ($load_current) {
            $operations = $prg['operation'] ?? null;
            $surgeons = $prg['surgeons'] ?? null;
            $clinics = $prg['clinics'] ?? null;
            $kinds = $prg['kinds'] ?? null;
        } else {
            $operations = $this->service->getOperationsByPatient($patient);
            $surgeons = $this->service->getAllSurgeons();
            $clinics = $this->service->getAllClinics();
            $kinds = $this->service->getAllKinds();
        }

        $token   = $guard->generateToken('patient_' . $patient_id);
        $form->get('csrf')->setValue($token);

        $url = $this->site->getUrl();
        $url .= '/patient/' . $patient_id;
        $qrcode = $this->code->generate($url);
        $photo = $this->getPhoto($patient);

        $form_photo = new ImageForm($patient_id);
        $form_photo->setAttribute('id', 'patient-photo-form');
        $form_photo->setAttribute('name', 'patient-photo-form');
        $form_photo->setAttribute('action', '/patient-photo/' . $patient_id);


        return new HtmlResponse($this->template->render(
            'patient::edit',
            [
                'patient'      => $patient,
                'form'         => $form,
                'identity'     => $identity,
                'operations'   => $operations,
                'qrcode'       => $qrcode,
                'surgeons'     => $surgeons,
                'clinics'      => $clinics,
                'form_photo'   => $form_photo,
                'photo'        => $photo,
                'kinds'        => $kinds,
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

    private function getPhoto($patient)
    {
        $image = $patient->getImage();
        if ($image) {
            $output = '/patient-photo/' . $patient->getPatientId();
        } else {
            $output = '/img/first.png';
        }
        return $output;
    }

}
