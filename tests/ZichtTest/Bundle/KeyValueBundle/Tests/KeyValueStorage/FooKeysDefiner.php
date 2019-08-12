<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace ZichtTest\Bundle\KeyValueBundle\Tests\KeyValueStorage;

use Zicht\Bundle\KeyValueBundle\KeyValueStorage\AbstractKeyDefiner;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedKey;

class FooKeysDefiner extends AbstractKeyDefiner
{
    /**
     * @return array|PredefinedKey[]
     */
    public function getPredefinedKeys()
    {
        return [
            PredefinedKey::createKey('foo-key', 'foo-value', 'The foo'),
            PredefinedKey::createKey('bar-key', 'bar-value', 'The bar'),
            PredefinedKey::createKey(
                'test-locale',
                ['nl' => 'Hallo Wereld', 'zz' => 'Garble garble', 'en' => 'Hello World'],
                'Locale dependent message',
                'zicht_locale_dependent_type',
                ['type' => 'text']
            ),
        ];
    }
}
