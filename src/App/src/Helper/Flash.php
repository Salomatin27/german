<?php
declare(strict_types=1);

namespace App\Helper;

use Laminas\View\Helper\AbstractHelper;
use Mezzio\Flash\FlashMessages;
use Mezzio\Session\Session;

class Flash extends AbstractHelper
{
    public function __invoke() : string
    {
        $session = session_status() === PHP_SESSION_ACTIVE
            ? $_SESSION
            : [];

        $flashMessages = FlashMessages::createFromSession(
            new Session($session)
        );

        //return $flashMessages->getFlashes();
        $messages = $flashMessages->getFlashes();
        $objects = '';
        foreach ($messages as $key => $message) {
            $object = '<div class="alert alert-dismissible alert-'
                .$key
                .'">'
                .'<button type="button" class="close" data-dismiss="alert">&times;</button>'
                .'<p class="mb-0">'
                .$message
                .'</p></div>';
            $objects .= $object;
        }
        return $objects;
    }
}