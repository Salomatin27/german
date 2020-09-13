<?php
declare(strict_types=1);

namespace Patient\Handler;

use Patient\Service\ImageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;

class DeleteImageHandler implements RequestHandlerInterface
{
    private $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService=$imageService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id', false);
        if (!$id) {
            return new TextResponse('not found');
        }
        $result = $this->imageService->deleteImage($id);

        return new TextResponse($result);
    }
}
