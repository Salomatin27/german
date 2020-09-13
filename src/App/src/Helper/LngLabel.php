<?php
declare(strict_types=1);

namespace App\Helper;

use Laminas\View\Helper\AbstractHelper;

class LngLabel extends AbstractHelper
{

    public function __invoke(string $baseLabel, string $secondLabel, bool $breakers = false) : string
    {
        $escaper = $this->getView()->plugin('escapehtml');
        $output = '';
        if (!empty($baseLabel)) {
            $output = $escaper($baseLabel);
        }
        if (!empty($secondLabel)) {
            $output = $output . ' '
                . ($breakers ? '<br>' : '')
                . '<span class="english-label text-secondary">'
                . $escaper($secondLabel) . '</span>';
        }


        return $output;
    }
}
