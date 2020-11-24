<?php
declare(strict_types=1);

namespace App\Helper;

use Psr\Http\Message\ServerRequestInterface;

class LngButtonFactory
{
    public function __invoke(ServerRequestInterface $request) : LngButton
    {
        return new LngButton($request);
    }
}