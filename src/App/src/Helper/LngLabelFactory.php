<?php
declare(strict_types=1);

namespace App\Helper;

use Psr\Http\Message\ServerRequestInterface;

class LngLabelFactory
{
    public function __invoke(ServerRequestInterface $request) : LngLabel
    {
        return new LngLabel($request);
    }
}