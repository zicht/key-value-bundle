<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Zicht\Bundle\KeyValueBundle\Entity\KeyValueStorage;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\Exception\KeyAlreadyExistsException;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\Exception\KeyNotFoundException;
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
     * @var string
     */
    private $webDirectory;

    /**
     * @var string
     */
    private $storageDirectory;

    /**
     * @var array
     */
    private $callsMade;

    /**
     * KeyValueStorageManager constructor.
     *
     * @param RegistryInterface $registry
     * @param string $webDirectory
     * @param string $storageDirectory
     */
    public function __construct(RegistryInterface $registry, $webDirectory, $storageDirectory)
    {
        $this->registry = $registry;
        $this->webDirectory = $webDirectory;
        $this->storageDirectory = $storageDirectory;
        $this->predefinedKeys = [];
        $this->callsMade = [];
    }

    /**
     * Returns the web directory which is publicly accessible
     *
     * @return string
     */
    public function getWebDirectory()
    {
        return $this->webDirectory;
    }

    /**
     * Returns the storage directory, which is inside the web directory, where key value files should be stored
     *
     * @return string
     */
    public function getStorageDirectory()
    {
        return $this->storageDirectory;
    }

    /**
     * @return array
     */
    public function getCallsMade()
    {
        return $this->callsMade;
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
     * Check if a PredefinedKey exists.
     *
     * @param string $key
     * @return boolean
     */
    public function hasPredefinedKey($key)
    {
        return array_key_exists($key, $this->predefinedKeys);
    }

    /**
     * Get a PredefinedKey.
     *
     * @param string $key
     * @return mixed|PredefinedKey
     * @throws KeyNotFoundException
     */
    public function getPredefinedKey($key)
    {
        if (!array_key_exists($key, $this->predefinedKeys)) {
            throw new KeyNotFoundException(sprintf('Key %s not found', $key));
        }
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
     * @throws KeyNotFoundException
     */
    public function getValue($key)
    {
        if ($entity = $this->getEntity($key)) {
            $value = $entity->getStorageValue();
        } else {
            $value = $this->getDefault($key);
        }

        // We need a way to know that this $key is associated to LocaleDependentData.
        // Unfortunately there is no clean/easy way to do this.  The check below uses
        // the form type, this works but is not ideal.
        $predefinedKey = $this->getPredefinedKey($key);
        if ('zicht_locale_dependent_type' === $predefinedKey->getFormType()) {
            $value = (new LocaleDependentData($value))->getValue();
        }

        $this->callsMade[$key] = $value;
        return $value;
    }

    /**
     * Save a value for a key.
     *
     * This will _always_ result in having a KeyValueStorage entity.
     * When a new Entity is created, it is only persisted, but not stored in the DB!
     *
     * @param $key
     * @param $value
     * @return KeyValueStorage
     */
    public function saveValue($key, $value)
    {
        if ($entity = $this->getEntity($key)) {
            $entity->setStorageValue($value);
        } else {
            $entity = new KeyValueStorage();
            $entity->setStorageKey($key);
            $entity->setStorageValue($value);
            $this->registry->getManager()->persist($entity);
        }
        return $entity;
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
