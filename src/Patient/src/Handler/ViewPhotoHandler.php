<?php
declare(strict_types=1);

namespace Patient\Handler;

use Laminas\Diactoros\Response;
use Mezzio\Template\TemplateRendererInterface;
use Patient\Service\ImageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ViewPhotoHandler implements RequestHandlerInterface
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
        $id = $request->getAttribute('id', false);
        if (!$id) {
            throw new \Exception('photo not found');
        }

        $image = $this->imageService->getPatientPhoto($id);

        return new Response($image['content'],200,[
            "Content-type"=> [$image['type']],
            "Content-length"=> [$image['size']],
        ]);
    }
}