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

    public function getCallsMade(): array
    {
        return $this->callsMade;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception\KeyNotFoundException
     */
    public function getValue(string $key)
    {
        $value = $this->keyValueStorageManager->getValue($key);
        $this->callsMade[$key] = $value;
        return $value;
    }

    public function purgeCachedItem(string $key): void
    {
        return $this->keyValueStorageManager->purgeCachedItem($key);
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->keyValueStorageManager, $name], $arguments);
    }
}
