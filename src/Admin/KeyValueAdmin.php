<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManagerInterface;

class KeyValueAdmin extends Admin
{
    /**
     * @var KeyValueStorageManager
     */
    private $storageManager;

    /** @var AdapterInterface */
    private $cache;

    /**
     * @param KeyValueStorageManager $storageManager
     */
    public function setStorageManager(KeyValueStorageManagerInterface $storageManager)
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
            ->add('storageKey', null, ['template' => '@ZichtKeyValue/Admin/cell_storageKey.html.twig'])
            ->add('storageValue', null, ['template' => '@ZichtKeyValue/Admin/cell_storageValue.html.twig'])
            ->add('friendlyName', null, ['template' => '@ZichtKeyValue/Admin/cell_friendlyName.html.twig'])
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);
        $filter->add('storageKey')->add('storageValue');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();

        if ($subject && $subject->getId()) {
            $form->add('storageKey', TextType::class, ['attr' => ['readonly' => true]]);
            if ($this->storageManager->hasPredefinedKey($subject->getStorageKey())) {
                $predefinedKey = $this->storageManager->getPredefinedKey($subject->getStorageKey());
                $formType = $predefinedKey->getFormType();
                $formOptions = $predefinedKey->getFormOptions();
            } else {
                $formType = TextType::class;
                $formOptions = [];
            }
            $form->add('storageValue', $formType, $formOptions);
        } else {
            $choices = [];
            foreach ($this->storageManager->getMissingDBKeys() as $value) {
                $choices[$value] = $value;
            }
            $preferedKey = urldecode($this->getRequest()->query->get('key'));
            $form->add('storageKey', ChoiceType::class, ['choices' => $choices, 'data' => $preferedKey]);
            // disable storageValue because we must first select a key for us to know the value type
            $form->add('storageValue', TextType::class, ['attr' => ['readonly' => true]]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist($subject)
    {
        // when we first persist the entity, we want the value to be the same as the predefined default value
        if ($this->storageManager->hasPredefinedKey($subject->getStorageKey())) {
            $predefinedKey = $this->storageManager->getPredefinedKey($subject->getStorageKey());
            $subject->setStorageValue($predefinedKey->getValue());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate($object)
    {
        // could also be implemented with doctrine lifecycle callbacks.
        $this->storageManager->purgeCachedItem($object->getStorageKey());
    }

    /**
     * {@inheritDoc}
     */
    public function postPersist($object)
    {
        $this->storageManager->purgeCachedItem($object->getStorageKey());
    }

    /**
     * {@inheritDoc}
     */
    public function postRemove($object)
    {
        $this->storageManager->purgeCachedItem($object->getStorageKey());
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate($subject)
    {
        // when the value is an UploadedFile ('file' form type) we will move it
        $value = $subject->getStorageValue();
        if ($value instanceof UploadedFile) {
            do {
                $targetFilePath = sprintf(
                    '%s/%s%s%s%s%s_%s',
                    $this->storageManager->getStorageDirectory(),
                    chr(rand(97, 122)),
                    chr(rand(97, 122)),
                    chr(rand(97, 122)),
                    chr(rand(97, 122)),
                    chr(rand(97, 122)),
                    $value->getClientOriginalName()
                );
            } while (file_exists($targetFilePath));

            $value->move(dirname($targetFilePath), basename($targetFilePath));

            // we store the relative path in the database
            $relativeFilePath = substr($targetFilePath, strlen($this->storageManager->getWebDirectory()));
            $subject->setStorageValue($relativeFilePath);

            // todo: we currently have no mechanism to remove files once they have been persisted
        }
    }
}
