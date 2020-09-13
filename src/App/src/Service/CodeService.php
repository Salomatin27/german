<?php
declare(strict_types=1);

namespace App\Service;

use QRcode;

class CodeService
{
    public function generate(string $text)
    {
        require_once 'phpqrcode.php';
        $img = QRcode::png($text);
        return $img;
    }
}
