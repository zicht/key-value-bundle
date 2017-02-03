<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;

/**
 * Class KeyValueAdmin.
 */
class KeyValueAdmin extends Admin
{
    /**
     * @var KeyValueStorageManager
     */
    private $storageManager;

    /**
     * @param KeyValueStorageManager $storageManager
     */
    public function setStorageManager(KeyValueStorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        parent::configureListFields($list);
        $list
            ->add('storageKey')
            ->add('storageValue')
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => []
                    ]
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        if ($this->getSubject() && $this->getSubject()->getId()) {
            $form->add('storageKey', 'text', ['read_only' => true]);
        } else {
            $choices = [];
            foreach ($this->storageManager->getMissingDBKeys() as $value) {
                $choices[$value] = $value;
            }
            $form->add('storageKey', 'choice', ['choices' => $choices, 'choices_as_values' => true]);
        }
        $form->add('storageValue', 'text');
    }
}
