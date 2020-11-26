<?php
namespace User\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

class UserPhoneFilter extends AbstractFilter
{
    public function filter($value)
    {
        //preg_match_all('!\d+!', $value, $new_value);
        $new_value = preg_replace('/[^0-9]/', '', $value);

        $len = strlen($new_value);
        if ($len>20) {
            return substr($new_value,-20);
        }
        if ($len==0) {
            return null;
        }

        return $new_value;
    }

}