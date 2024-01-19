<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

interface KeysDefinerInterface
{
    /**
     * Returns an array with 1 or more Predefined Keys.
     *
     * @return PredefinedKeyInterface[]
     */
    public function getPredefinedKeys();

    /**
     * Set the default values configured in `zicht_key_value.defaults`
     */
    public function setDefaultValues(array $defaultValues);
}
