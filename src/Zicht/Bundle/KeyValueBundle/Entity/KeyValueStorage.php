<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class KeyValue
 * @final
 *
 * @ORM\Entity()
 * @ORM\Table(name="zicht_keyvalue_keyvaluestorage")
 */
class KeyValueStorage
{
    /**
     * @var int
     *
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
     * @return int
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
     * @return array|null
     */
    public function getStorageValue()
    {
        return $this->storageValue;
    }

    /**
     * @param array|string $storageValue
     */
    public function setStorageValue($storageValue)
    {
        $this->storageValue = $storageValue;
    }
}
