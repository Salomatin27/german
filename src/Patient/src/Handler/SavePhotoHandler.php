<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Form\ImageForm;
use Patient\Service\ImageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;

class SavePhotoHandler implements RequestHandlerInterface
{
    private $imageService;
    private $template;

    public function __construct(ImageService $imageService, TemplateRendererInterface $templateRenderer)
    {
        $this->imageService = $imageService;
        $this->template = $templateRenderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // patient_id
        $id = $request->getAttribute('id', false);
        $data = array_merge_recursive($_FILES, $request->getUploadedFiles());


        $form = new ImageForm($id);
        $form->setData($data);
        if ($form->isValid()) {
            $data = $form->getData();
            $this->imageService->setPatientPhoto($data, $id);

        }

        return new RedirectResponse('/patient/' . $id);
    }
}
