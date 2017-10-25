<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleDependentDataType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('type');
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zicht_bundle_key_value.form_type.locale_dependent_data_type';
    }
}
