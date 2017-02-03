<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace ZichtTest\Bundle\KeyValueBundle\Tests\KeyValueStorage;

use Zicht\Bundle\KeyValueBundle\KeyValueStorage\AbstractKeysDefiner;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedKey;

class FooKeysDefiner extends AbstractKeysDefiner
{
    public function __construct()
    {
        $this->addKeys();
    }

    private function addKeys()
    {
        $key1 = PredefinedKey::createKey('foo-key', 'foo-value', 'The foo');
        $key2 = PredefinedKey::createKey('bar-key', 'bar-value', 'The bar');
        $this->predefinedKeys[] = $key1;
        $this->predefinedKeys[] = $key2;
    }
}