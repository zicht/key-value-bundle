<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace ZichtTest\Bundle\KeyValueBundle\Tests\KeyValueStorage;

use Zicht\Bundle\KeyValueBundle\KeyValueStorage\AbstractKeyDefiner;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedKey;

class FooKeysDefiner extends AbstractKeyDefiner
{
    public function getPredefinedKeys()
    {
        return [
            PredefinedKey::createKey('foo-key', 'foo-value', 'The foo'),
            PredefinedKey::createKey('bar-key', 'bar-value', 'The bar')
        ];
    }
}
