<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;

class KeyValueBundleDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var KeyValueStorageManager
     */
    private $keyValueStorageManager;

    public function __construct(KeyValueStorageManager $keyValueStorageManager)
    {
        $this->keyValueStorageManager = $keyValueStorageManager;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zicht_bundle_key_value.key_value_bundle_data_collector';
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