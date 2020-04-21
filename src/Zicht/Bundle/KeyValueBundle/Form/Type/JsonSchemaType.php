<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Form\Type;

use Swaggest\JsonSchema\Schema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsonSchemaType extends AbstractType
{
    /** @var Schema|null */
    private $schema;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('json_schema_file');
        $resolver->setDefault('popup', false);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Transform data to and from a string
        $builder->addModelTransformer(new CallbackTransformer(
            function ($dataAsObject) {
                return json_encode($dataAsObject);
            },
            function ($dataAsString) {
                return json_decode($dataAsString, true);
            }
        ));

        // Validate the data
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
            try {
                $this->getSchema($options['json_schema_file'])->in(json_decode($event->getData()));
            } catch (\Exception $exception) {
                $event->getForm()->addError(new FormError($exception->getMessage()));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = [
            'class' => 'js-json-editor',
            'data-json-editor-popup-title' => basename($options['json_schema_file']),
            'data-json-editor-popup' => $options['popup'] ? 'yes' : 'no',
            'data-json-editor-options' => '{"theme": "bootstrap3", "required_by_default": true, "disable_properties": true}',
            'data-json-editor-schema-url' => $this->getSchema($options['json_schema_file'])->offsetGet('$id'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zicht_json_schema_type';
    }

    /**
     * Returns the schema instance
     *
     * @param string $file
     * @return Schema|\Swaggest\JsonSchema\SchemaContract|null
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    private function getSchema($file)
    {
        if ($this->schema === null) {
            $this->schema = Schema::import(json_decode(file_get_contents($file)));
        }
        return $this->schema;
    }
}
