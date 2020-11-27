<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

class PredefinedJsonSchemaKey implements PredefinedKeyInterface
{
    /**
     * Create a key based on a json schema
     *
     * @param string $jsonSchemaFile
     * @param bool|int|float|string|array $staticUncheckedDefaultValue
     * @return PredefinedJsonSchemaKey
     */
    public static function createKey(string $jsonSchemaFile, $staticUncheckedDefaultValue = null)
    {
        return new self($jsonSchemaFile, $staticUncheckedDefaultValue === null ? (object)[] : $staticUncheckedDefaultValue);
    }

    /** @var string $jsonSchemaFile */
    private $jsonSchemaFile;

    /** @var bool|int|float|string|array|object */
    private $staticUncheckedDefaultValue;

    /** @var SchemaContract|null */
    private $schema;

    /**
     * PredefinedKey constructor.
     *
     * Disable constructing, they can only be created from self::createKey to ensure key/value immutability.
     */
    private function __construct(string $jsonSchemaFile, $staticUncheckedDefaultValue)
    {
        $this->jsonSchemaFile = $jsonSchemaFile;
        $this->staticUncheckedDefaultValue = $staticUncheckedDefaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return basename($this->jsonSchemaFile);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->migrate($this->staticUncheckedDefaultValue);
    }

    /**
     * {@inheritDoc}
     */
    public function getFriendlyName()
    {
        $schema = $this->getSchema();
        return $schema->description ?? $schema->title ?? $this->getKey();
    }

    /**
     * {@inheritDoc}
     */
    public function getFormType()
    {
        return 'zicht_json_schema_type';
    }

    /**
     * {@inheritDoc}
     */
    public function getFormOptions()
    {
        return ['schema' => $this->getSchema()];
    }

    /**
     * Try to migrate a given value to a new value
     *
     * @param bool|int|float|string|array $value
     * @return bool|int|float|string|array|null Returns null when the validation failed
     */
    public function migrate($value)
    {
        $context = new Context();
        $context->skipValidation = true;

        // json_encode and then json_decode to return object structure instead of php array structure because the schema uses objects
        $objectValue = json_decode(json_encode($value), false);

        // PHP is unable to distinguish between an empty array and an empty object,
        // that causes `[]` to become an empty array, while the schema expects an
        // empty object.  We fix this case manually and hope this does not occur
        // anywhere else.
        if ($objectValue === []) {
            $objectValue = (object)$objectValue;
        }

        // json_encode and then json_decode to return php array structure instead of object structure because the key-value-bundle uses php arrays
        return json_decode(json_encode($this->getSchema()->process($objectValue, $context)), true);
    }

    /**
     * Returns true when $value conforms to the schema
     *
     * @param array $value
     * @param null|string &$errorMessage Returns the error message, if any
     * @return bool
     */
    public function isValid(array $value, &$errorMessage = null): bool
    {
        // PHP is unable to distinguish between an empty array and an empty object,
        // that causes `[]` to become an empty array, while the schema expects an
        // empty object.  We fix this case manually and hope this does not occur
        // anywhere else.
        if ($value === []) {
            $value = (object)$value;
        }

        try {
            // json_encode and then json_decode to return object structure instead of php array structure because the schema uses objects
            $this->getSchema()->in(json_decode(json_encode($value), false));
            return true;
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     * @return SchemaContract
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    private function getSchema()
    {
        if ($this->schema === null) {
            $this->schema = Schema::import(json_decode(file_get_contents($this->jsonSchemaFile)));
        }
        return $this->schema;
    }
}
