<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

/**
 * Class PredefinedKey.
 *
 * Definition of a Predefined-key.
 */
class PredefinedKey
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
    public static function createKey($key, $value = null, $friendlyName = null, $formType = 'text', array $formOptions = [])
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
     * Create a key based on a json schema
     * Values can be scalar or array.
     *
     * @param string $jsonSchemaFile
     * @param mixed $value
     * @param string|null $friendlyName
     * @return PredefinedKey
     */
    public static function createJsonSchemaKey($jsonSchemaFile, $value = null, $friendlyName = null)
    {
        $instance = new self();
        $instance->setKey(basename($jsonSchemaFile));
        $instance->setValue($value);
        $instance->setFriendlyName($friendlyName);
        $instance->setFormType("zicht_json_schema_type");
        $instance->setFormOptions(['json_schema_file' => $jsonSchemaFile]);
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
     * Disable constructing, they can only be created from self::createKey to ensure key/value immutablilty.
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
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return array
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }
}
