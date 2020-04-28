<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class PredefinedKey.
 *
 * Definition of a Predefined-key.
 */
class PredefinedKey implements PredefinedKeyInterface
{
    /**
     * Create a key.
     * Values can be scalar or array.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $friendlyName
     * @param string $formType
     * @param array $formOptions
     * @return PredefinedKey
     */
    public static function createKey($key, $value = null, $friendlyName = null, $formType = TextType::class, array $formOptions = [])
    {
        $instance = new self();
        $instance->setKey($key);
        $instance->setValue($value);
        $instance->setFriendlyName($friendlyName);
        $instance->setFormType($formType);
        $instance->setFormOptions($formOptions);
        return $instance;
    }

    /**
     * The unique identifier for this key.
     * E.g.: "vendor.bundle.domain.key"
     *
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Humanly readable representation of what this key is.
     *
     * @var string
     */
    private $friendlyName;

    /**
     * Form type to use when storing the value
     *
     * @var string
     */
    private $formType;

    /**
     * Form options to use when storing the value
     *
     * @var array
     */
    private $formOptions;

    /**
     * PredefinedKey constructor.
     *
     * Disable constructing, they can only be created from self::createKey to ensure key/value immutability.
     */
    private function __construct()
    {
    }

    /**
     * @param string $key
     */
    private function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param mixed $value
     */
    private function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $friendlyName
     */
    private function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;
    }

    /**
     * @param string $formType
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;
    }

    /**
     * @param array $formOptions
     */
    public function setFormOptions($formOptions)
    {
        $this->formOptions = $formOptions;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }
}
