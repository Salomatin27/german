<?php
namespace App\Filter;

use Laminas\Filter\AbstractFilter;

// Этот класс фильтра предназначен для преобразования произвольного номера телефона в
// локальный или международный формат.
class PhoneFilter extends AbstractFilter
{
    // Константы форматов номера.
    const PHONE_FORMAT_LOCAL = 'local'; // Local phone format
    const PHONE_FORMAT_INTL  = 'intl';  // International phone format

    // Доступные опции фильтра.
    protected $options = [
        'format' => self::PHONE_FORMAT_INTL
    ];

    // Конструктор.
    public function __construct($options = null)
    {
        // Задает опции фильтра (если они предоставлены).
        if (is_array($options)) {
            if (isset($options['format'])) {
                $this->setFormat($options['format']);
            }
        }
    }

    // Задает формат номера.
    public function setFormat($format)
    {
        // Проверяет входной аргумент.
        if ($format!=self::PHONE_FORMAT_LOCAL &&
            $format!=self::PHONE_FORMAT_INTL ) {
            throw new \Exception('Invalid format argument passed.');
        }

        $this->options['format'] = $format;
    }

    // Возвращает формат номера.
    public function getFormat()
    {
        return $this->options['format'];
    }

    // Фильтрует телефонный номер.
    public function filter($value)
    {
        if (!is_scalar($value)) {
            // Возвращаем нескалярное значение неотфильтрованным.
            return $value;
        }

        $value = (string)$value;

        if (strlen($value)==0) {
            // Возвращаем пустое значение неотфильтрованным.
            return $value;
        }

        // Сперва удаляем все нецифровые символы.
        $digits = preg_replace('#[^0-9]#', '', $value);

        $format = $this->options['format'];

        if ($format == self::PHONE_FORMAT_INTL) {
            // Дополняем нулями, если число цифр некорректно.
            $digits = str_pad($digits, 10, "0", STR_PAD_LEFT);

            // Добавляем скобки, пробелы и тире.
            $len = strlen($digits);
            if ($len>12) {
                $digits = substr($digits, -12);
                $len =12;
            }
            $phoneNumber = substr($digits, 0, $len-10) . ' (' .
                substr($digits, $len-10, 3) . ') ' .
                substr($digits, $len-7, 3) . '-' .
                substr($digits, $len-4, 4);
        } else { // self::PHONE_FORMAT_LOCAL
            // Дополняем нулями, если число цифр некорректно
            $digits = str_pad($digits, 7, "0", STR_PAD_LEFT);

            $len = strlen($digits);
            if ($len>7) {
                $digits = substr($digits, -7);
                $len =7;
            }
            // Добавляем тире.
            $phoneNumber = substr($digits, 0, 3) . '-'. substr($digits, 3, 4);
        }

        return $phoneNumber;
    }
}
