<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

/**
 * Class LocaleDependentData
 *
 * This class can be used to wrap the json encoded data formed by zicht_locale_dependent_type form type.
 * Its purpose is to return the value of just the current locale.
 */
class LocaleDependentData
{
    /** @var string */
    protected static $locale;

    /** @var mixed[] */
    protected $data;

    /**
     * The locale is set from the request, using a KernelListener
     *
     * @param string $locale
     */
    public static function setLocale($locale)
    {
        self::$locale = $locale;
    }

    /**
     * The data must be in the form ['nl' => mixed, 'en' => mixed, ...]
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns the value associated to request->locale, if any
     *
     * @return mixed|null
     */
    public function getValue()
    {
        if (array_key_exists(self::$locale, $this->data)) {
            return $this->data[self::$locale];
        }

        return null;
    }

    /**
     * Returns the values for all locales
     *
     * @return array|mixed[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the string representation of getValue()
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}
