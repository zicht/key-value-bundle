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
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('schema');
        $resolver->setAllowedTypes('schema', Schema::class);

        $resolver->setDefault('popup', false);
        $resolver->setAllowedTypes('popup', 'bool');
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
                $options['schema']->in(json_decode($event->getData()));
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
            'data-json-editor-popup-title' => $options['schema']['$id'],
            'data-json-editor-popup' => $options['popup'] ? 'yes' : 'no',
            'data-json-editor-options' => '{"theme": "bootstrap3", "required_by_default": true, "disable_properties": true}',
            'data-json-editor-schema-url' => $options['schema']['$id'],
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
}
