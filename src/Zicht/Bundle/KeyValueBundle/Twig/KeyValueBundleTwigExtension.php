<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Twig;

use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedKey;

/**
 * Class KeyValueBundleTwigExtension.
 */
class KeyValueBundleTwigExtension extends \Twig_Extension
{
    /**
     * @var KeyValueStorageManager
     */
    private $storageManager;

    /**
     * KeyValueBundleTwigExtension constructor.
     *
     * @param KeyValueStorageManager $storageManager
     */
    public function __construct(KeyValueStorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zicht_key_value_twigextension';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getFromKeyValueStorage', array($this, 'getFromKeyValueStorage')),
            new \Twig_SimpleFunction('getPredefinedKey', array($this, 'getPredefinedKey')),
            new \Twig_SimpleFunction('getMissingDBValues', array($this, 'getMissingDBValues')),
        ];
    }

    /**
     * Gets value for given $key from the StorageManager.
     *
     * @param string $key
     * @return array|mixed
     */
    public function getFromKeyValueStorage($key)
    {
        return $this->storageManager->getValue($key);
    }

    /**
     * @param $key
     * @return mixed|PredefinedKey
     */
    public function getPredefinedKey($key)
    {
        return $this->storageManager->getPredefinedKey($key);
    }

    public function getMissingDBValues()
    {
        return $this->storageManager->getMissingDBKeys();
    }
}
