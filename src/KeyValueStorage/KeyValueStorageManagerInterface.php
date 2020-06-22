<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

interface KeyValueStorageManagerInterface
{
    public function getValue(string $key);

    public function purgeCachedItem(string $key);
}
