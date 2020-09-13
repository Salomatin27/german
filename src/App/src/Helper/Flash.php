<?php
declare(strict_types=1);

namespace App\Helper;

use Laminas\View\Helper\AbstractHelper;
use Mezzio\Flash\FlashMessages;
use Mezzio\Session\Session;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;

class Flash extends AbstractHelper
{

    public function __invoke(): string
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $flashMessages = FlashMessages::createFromSession(
            new Session($_SESSION)
        );

        $messages = $flashMessages->getFlashes();
        //$flashMessages->clearFlash();
        $objects = '';
        foreach ($messages as $key => $message) {
            $object = '<div class="alert alert-dismissible alert-'
                . $key
                . '">'
                . '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                . '<p class="mb-0">'
                . $message
                . '</p></div>';
            $objects .= $object;
        }
        return $objects;
    }
}
