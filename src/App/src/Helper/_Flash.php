<?php

declare(strict_types = 1);

namespace App\Helper;

use Laminas\View\Helper\AbstractHelper;
use Mezzio\Flash\FlashMessages;

class _Flash extends AbstractHelper
{
    /** @var FlashMessages $flash */
    private $flash;

    public function setFlash(FlashMessages $flashMessages)
    {
        $this->flash = $flashMessages;
    }

    public function __invoke() :string
    {
        if (!$this->flash) {
            return '';
        }
        $messages = $this->flash->getFlashes();
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
