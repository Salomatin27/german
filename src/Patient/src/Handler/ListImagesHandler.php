<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Form\ImageForm;
use Patient\Service\ImageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListImagesHandler implements RequestHandlerInterface
{
    private $imageService;
    private $template;

    public function __construct(ImageService $imageService, TemplateRendererInterface $templateRenderer)
    {
        $this->template = $templateRenderer;
        $this->imageService = $imageService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // operation_id
        $id = $request->getAttribute('id', false);
        if (!$id) {
            throw new \Exception('operation not found');
        }

        $images = $this->imageService->getImages($id);
        $form = new ImageForm($id);


        return new HtmlResponse($this->template->render('patient::list-images', [
            'layout' => false,
            'form'   => $form,
            'images' => $images,
            'id'     => $id,
        ]));
    }
}
