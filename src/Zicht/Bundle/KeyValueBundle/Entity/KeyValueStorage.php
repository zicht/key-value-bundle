<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class KeyValue
 *
 * @ORM\Entity()
 * @ORM\Table(name="zicht_keyvalue_keyvaluestorage")
 */
final class KeyValueStorage
{
    /**
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column
     */
    private $storageKey;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $storageValue;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }

    /**
     * @param string $storageKey
     */
    public function setStorageKey($storageKey)
    {
        $this->storageKey = $storageKey;
    }

    /**
     * @return array
     */
    public function getStorageValue()
    {
        return $this->storageValue;
    }

    /**
     * @param array $storageValue
     */
    public function setStorageValue($storageValue)
    {
        $this->storageValue = $storageValue;
    }
}
