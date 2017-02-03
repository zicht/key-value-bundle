<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

/**
 * Interface KeysDefinerInterface.
 */
interface KeysDefinerInterface
{
    /**
     * Returns an array with 1 or more Predefined Keys.
     *
     * @return PredefinedKey[]
     */
    public function getPredefinedKeys();
}
