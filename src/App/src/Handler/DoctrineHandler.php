<?php
declare(strict_types=1);

namespace App\Handler;

use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DoctrineHandler implements RequestHandlerInterface
{
    private $em;
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //$hydrator = new DoctrineObject($this->em);
        $sql = 'select count(*) from user';
        $statement = $this->em->getConnection()->query($sql);
        $count = $statement->fetchColumn(0);
        return new JsonResponse(['count users' => $count]);
    }

}