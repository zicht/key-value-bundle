<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

abstract class AbstractKeyDefiner implements KeysDefinerInterface
{
    /**
     * @var array
     */
    protected $defaultValues = [];

    /**
     * {@inheritDoc}
     */
    public function setDefaultValues(array $defaultValues)
    {
        $this->defaultValues = $defaultValues;
    }

    /**
     * @param string $key
     * @param mixed $fallbackValue
     * @return mixed
     */
    protected function getDefaultValue($key, $fallbackValue)
    {
        return array_key_exists($key, $this->defaultValues) ? $this->defaultValues[$key] : $fallbackValue;
    }
}
