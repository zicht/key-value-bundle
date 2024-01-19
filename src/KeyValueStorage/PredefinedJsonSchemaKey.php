<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Swaggest\JsonSchema\SchemaContract;
use Zicht\Bundle\FrameworkExtraBundle\Form\JsonSchemaType;
use Zicht\Bundle\FrameworkExtraBundle\JsonSchema\SchemaService;

class PredefinedJsonSchemaKey implements PredefinedKeyInterface
{
    /**
     * @param SchemaService $schemaService
     * @param string $schemaFile
     * @param bool|int|float|string|array $staticUncheckedDefaultValue
     * @return PredefinedJsonSchemaKey
     */
    public static function createKey($schemaService = null, $schemaFile = null, $staticUncheckedDefaultValue = null)
    {
        $args = func_get_args();
        if ($args[0] instanceof SchemaService) {
            $schemaService = $args[0];
            $schemaFile = $args[1];
            $staticUncheckedDefaultValue = isset($args[2]) ? $args[2] : null;
        } else {
            $schemaService = new SchemaService(new DummyTranslator(), '/unspecified/directory');
            $schemaFile = $args[0];
            $staticUncheckedDefaultValue = isset($args[1]) ? $args[1] : null;
        }
        return new self($schemaService, $schemaFile, $staticUncheckedDefaultValue === null ? (object)[] : $staticUncheckedDefaultValue);
    }

    /** @var SchemaService */
    private $schemaService;

    /** @var string */
    private $schemaFile;

    /** @var bool|int|float|string|array|object */
    private $staticUncheckedDefaultValue;

    /** @var SchemaContract|null */
    private $schema;

    /**
     * Disable constructing, they can only be created from self::createKey to ensure key/value immutability.
     *
     * @param bool|int|float|string|array|object $staticUncheckedDefaultValue
     */
    private function __construct(SchemaService $schemaService, string $schemaFile, $staticUncheckedDefaultValue)
    {
        $this->schemaService = $schemaService;
        $this->schemaFile = $schemaFile;
        $this->staticUncheckedDefaultValue = $staticUncheckedDefaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return basename($this->schemaFile);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->schemaService->migrate($this->schemaFile, $this->staticUncheckedDefaultValue);
    }

    /**
     * {@inheritDoc}
     */
    public function getFriendlyName()
    {
        $schema = $this->schemaService->getSchema($this->schemaFile);
        return $schema->description ?? $schema->title ?? $this->getKey();
    }

    /**
     * {@inheritDoc}
     */
    public function getFormType()
    {
        return JsonSchemaType::class;
    }

    /**
     * Try to migrate a given value to a new value
     *
     * @param bool|int|float|string|array $value
     * @param string|null $message
     * @return bool|int|float|string|array|null Returns null when the validation failed
     */
    public function migrate($value, &$message = null)
    {
        return $this->schemaService->migrate($this->schemaFile, $value, $message);
    }

    /**
     * Try to migrate a given value to a new value
     *
     * @param bool|int|float|string|array $value
     * @param string|null $message Returns the error message, if any
     * @return bool Returns false when the validation failed
     */
    public function isValid($value, &$message = null): bool
    {
        return $this->schemaService->validate($this->schemaFile, $value, $message);
    }

    /**
     * {@inheritDoc}
     */
    public function getFormOptions()
    {
        return [
            'schema' => $this->schemaFile,
            'debug' => true,
            'options' => [
                'ajax' => true,
                'disable_collapse' => true,
                'disable_edit_json' => true,
                'disable_properties' => true,
                'display_required_only' => false,
                'required_by_default' => true,
                'theme' => 'bootstrap4',
            ],
        ];
    }
}
