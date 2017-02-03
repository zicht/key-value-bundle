<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3-2-17
 * Time: 9:26
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
