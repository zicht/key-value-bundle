<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

class KeyValueStorageManagerDebugWrapper implements KeyValueStorageManagerInterface, KeyValueStorageManagerDebugInterface
{
    /**
     * @var KeyValueStorageManager
     */
    private $keyValueStorageManager;

    /**
     * @var array
     */
    private $callsMade;

    public function __construct(KeyValueStorageManager $keyValueStorageManager)
    {
        $this->keyValueStorageManager = $keyValueStorageManager;
        $this->callsMade = [];
    }

    public function getCallsMade()
    {
        return $this->callsMade;
    }

    public function getValue(string $key)
    {
        $value = $this->keyValueStorageManager->getValue($key);
        $this->callsMade[$key] = $value;
        return $value;
    }

    public function purgeCachedItem(string $key)
    {
        return $this->keyValueStorageManager->purgeCachedItem($key);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->keyValueStorageManager, $name), $arguments);
    }
}
