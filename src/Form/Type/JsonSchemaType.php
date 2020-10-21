<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Form\Type;

use Swaggest\JsonSchema\Schema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('schema');
        $resolver->setAllowedTypes('schema', Schema::class);

        $resolver->setDefault('popup', false);
        $resolver->setAllowedTypes('popup', 'bool');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Transform data to and from a string
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($dataAsObject) {
                    return json_encode($dataAsObject);
                },
                function ($dataAsString) {
                    return json_decode($dataAsString, true);
                }
            )
        );

        // Validate the data
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($options) {
            try {
                $options['schema']->in(json_decode($event->getData()));
            } catch (\Exception $exception) {
                $event->getForm()->addError(new FormError($exception->getMessage()));
            }
            }
        );
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'zicht_json_schema_type';
    }
}
