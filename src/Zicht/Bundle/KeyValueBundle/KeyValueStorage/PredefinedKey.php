<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

/**
 * Class PredefinedKey.
 *
 * Definition of a Predefined-key.
 */
class PredefinedKey
{
    /**
     * Create a key.
     * Values can be scalar or array.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $friendlyName
     * @return PredefinedKey
     */
    public static function createKey($key, $value = null, $friendlyName = null)
    {
        $instance = new self();
        $instance->setKey($key);
        $instance->setValue($value);
        $instance->setFriendlyName($friendlyName);
        return $instance;
    }

    /**
     * The unique identifier for this key.
     * E.g.: "vendor.bundle.domain.key"
     *
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Humanly readable representation of what this key is.
     *
     * @var string
     */
    private $friendlyName;

    /**
     * PredefinedKey constructor.
     *
     * Disable constructing, they can only be created from self::createKey to ensure key/value immutablilty.
     */
    private function __construct()
    {
    }

    /**
     * @param string $key
     */
    private function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param mixed $value
     */
    private function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $friendlyName
     */
    private function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }
}
