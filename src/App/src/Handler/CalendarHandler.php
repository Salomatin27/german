<?php
declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CalendarHandler implements RequestHandlerInterface
{
    private $em;
    private $template;

    public function __construct(TemplateRendererInterface $template, EntityManager $em)
    {

        $this->template = $template;
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = $request->getAttribute(UserInterface::class, null);

        // надо привязать доктора к пользователю?!
        $dql = 'Select opr From \App\Entity\Operation opr Where opr.date >= current_date() Order by opr.date asc';
        $query = $this->em->createQuery($dql)->setMaxResults(100);
        $result = $query->getResult();

        return new HtmlResponse($this->template->render('app::calendar', [
            'identity' => $identity,
            'items'    => $result,
        ]));
    }
}
