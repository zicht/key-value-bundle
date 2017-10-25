<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

class LocaleDependentData
{
    /** @var string */
    protected static $locale;

    /** @var mixed[] */
    protected $data;

    public static function setLocale($locale)
    {
        self::$locale = $locale;
    }

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getValue()
    {
        if (array_key_exists(self::$locale, $this->data)) {
            return $this->data[self::$locale];
        }

        if (sizeof($this->data)) {
            return current($this->data);
        }

        return null;
    }

    public function __toString()
    {
        return (string)$this->getValue();
    }
}
