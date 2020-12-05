<?php
declare(strict_types=1);

namespace App\Helper;

use Laminas\View\Helper\AbstractHelper;

class LngButton extends AbstractHelper
{

    public function __invoke(
        string $baseLabel,
        string $secondLabel,
        bool $hideText = false,
        bool $breakers = false
    ) : string {

        if ($hideText) {
            $span_class = 'd-none d-sm-block';
        } else {
            $span_class = 'd-block';
        }
        $escaper = $this->getView()->plugin('escapehtml');
        $output = '';
        if (!empty($baseLabel)) {
            $output = $escaper($baseLabel);
        }
        if (!empty($secondLabel)) {
            $output = $output . ' '
                . ($breakers ? '<br>' : '')
                . '<span class="english-label text-primaty">'
                . '(' . $escaper($secondLabel) . ')</span>';
        }

        $output = '<span class="' . $span_class . '">' . $output . '</span>';

        return $output;
    }
}
