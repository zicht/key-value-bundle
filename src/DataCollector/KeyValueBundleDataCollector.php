<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManagerDebugInterface;

class KeyValueBundleDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var KeyValueStorageManagerDebugInterface
     */
    private $keyValueStorageManager;

    public function __construct(KeyValueStorageManagerDebugInterface $keyValueStorageManager)
    {
        $this->keyValueStorageManager = $keyValueStorageManager;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zicht_key_value';
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->data = ['key_value_calls' => []];
    }

    /**
     * @return void
     */
    public function lateCollect()
    {
        $this->data = ['key_value_calls' => $this->keyValueStorageManager->getCallsMade()];
    }

    /**
     * @return array
     */
    public function getCallsMade()
    {
        return $this->data['key_value_calls'] ?? [];
    }
}
