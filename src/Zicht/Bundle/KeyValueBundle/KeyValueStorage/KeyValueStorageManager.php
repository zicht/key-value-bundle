<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Zicht\Bundle\KeyValueBundle\Entity\KeyValueStorage;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\Exception\KeyAlreadyExistsException;
use Zicht\Itertools as iter;

/**
 * Class KeyValueStorageManager.
 *
 * This class is responsible for handling questions in the parameters-domain.
 */
class KeyValueStorageManager
{
    /**
     * Holds all added predefined keys.
     * Predefind keys are added with self#addKeysDefiner.
     * This array will have Predefined->getKey() as index.
     *
     * @var PredefinedKey[]
     */
    private $predefinedKeys;

    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * KeyValueStorageManager constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->predefinedKeys = [];
    }

    /**
     * Returns the strings of the defined keys.
     *
     * @return array
     */
    public function getAllKeys()
    {
        return array_keys($this->predefinedKeys);
    }

    /**
     * Get a PredefinedKey.
     *
     * @param string $key
     * @return mixed|PredefinedKey
     */
    public function getPredefinedKey($key)
    {
        return $this->predefinedKeys[$key];
    }

    /**
     * This method accepts a KeysDefinerInterface and will extract all PredefinedKeys to $this->predefinedKeys.
     *
     * @param KeysDefinerInterface $keysDefiner
     * @throws KeyAlreadyExistsException
     */
    public function addKeysDefiner(KeysDefinerInterface $keysDefiner)
    {
        foreach ($keysDefiner->getPredefinedKeys() as $predefinedKey) {
            if (array_key_exists($predefinedKey->getKey(), $this->predefinedKeys)) {
                throw new KeyAlreadyExistsException(sprintf("The key %s is already added", $predefinedKey->getKey()));
            }
            $this->predefinedKeys[$predefinedKey->getKey()] = $predefinedKey;
        }
    }

    /**
     * Gets the list of keys that are not present in the DB.
     *
     * @return array
     */
    public function getMissingDBKeys()
    {
        // get all DB keys. Maybe change this to simple query to prevent hydrating json-fields.
        $dbKeys = iter\map(
            function (KeyValueStorage $el) {
                return $el->getStorageKey();
            },
            $this->getAllEntities()
        )
            ->toArray();
        return array_diff(array_keys($this->predefinedKeys), $dbKeys);
    }

    /**
     * Gets the value for given $key.
     * Either the DB-value or the default value.
     *
     * @param string $key
     * @return array|mixed
     */
    public function getValue($key)
    {
        if ($entity = $this->getEntity($key)) {
            return $entity->getStorageValue();
        }
        return $this->getDefault($key);
    }

    /**
     * Fetches the DB-representation of given $key.
     * Can also return null when entity not exists.
     *
     * @param string $key
     * @return null|KeyValueStorage|object
     */
    private function getEntity($key)
    {
        return $this->getRepository()->findOneBy(['storageKey' => $key]);
    }

    /**
     * Gets all entities from the database.
     *
     * @return KeyValueStorage[]
     */
    private function getAllEntities()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Gets the value from the PredefinedKey.
     *
     * @param string $key
     * @return mixed
     */
    private function getDefault($key)
    {
        return $this->predefinedKeys[$key]->getValue();
    }

    /**
     * @return ObjectRepository
     */
    private function getRepository()
    {
        return $this->registry->getRepository('ZichtKeyValueBundle:KeyValueStorage');
    }
}