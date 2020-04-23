<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

class PredefinedJsonSchemaKey implements PredefinedKeyInterface
{
    /**
     * Create a key based on a json schema
     *
     * @param string $jsonSchemaFile
     * @return PredefinedJsonSchemaKey
     */
    public static function createKey($jsonSchemaFile)
    {
        return new self($jsonSchemaFile);
    }

    /** @var string $jsonSchemaFile */
    private $jsonSchemaFile;

    /** @var SchemaContract|null */
    private $schema;

    /**
     * PredefinedKey constructor.
     *
     * Disable constructing, they can only be created from self::createKey to ensure key/value immutability.
     */
    private function __construct($jsonSchemaFile)
    {
        $this->jsonSchemaFile = $jsonSchemaFile;
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
        return $this->getSchema()->in((object)[])->toArray();
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
