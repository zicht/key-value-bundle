<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace ZichtTest\Bundle\KeyValueBundle\Tests\KeyValueStorage;

use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedKey;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeysDefinerInterface;

class FooKeysDefiner implements KeysDefinerInterface
{
    public function getPredefinedKeys()
    {
        return [
            PredefinedKey::createKey('foo-key', 'foo-value', 'The foo'),
            PredefinedKey::createKey('bar-key', 'bar-value', 'The bar')
        ];
    }
}
