<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Command;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedJsonSchemaKey;

class KeyValueMigrateJsonSchemaKeysCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('zicht:key-value:migrate-json-schema-keys')
            ->setDescription('Validate all json schema keys and migrate when possible')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force migration in the database to occur');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Key', 'Default', 'Storage', 'Migrate']);

        /** @var KeyValueStorageManager $storageManager */
        $storageManager = $this->getContainer()->get('zicht_bundle_key_value.key_value_storage_manager');
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');

        foreach ($storageManager->getAllKeys() as $key) {
            $predefinedKey = $storageManager->getPredefinedKey($key);
            if ($predefinedKey instanceof PredefinedJsonSchemaKey) {
                $key = $predefinedKey->getKey();
                $defaultValue = $predefinedKey->getValue();
                if (!$defaultValueIsValid = $predefinedKey->isValid($defaultValue, $message)) {
                    $output->writeln(sprintf('<error>%s</error> - %s - validation error on default value', $message, $key));
                }
                $defaultState = $defaultValueIsValid ? 'ok' : '<error>invalid</error>';
                $storageValue = $storageManager->getValue($key);
                if(!$storageValueIsValid = $predefinedKey->isValid($storageValue, $message)) {
                    $output->writeln(sprintf('<error>%s</error> - %s - validation error on storage value', $message, $key));
                }
                $storageState = $storageValueIsValid ? 'ok' : '<error>invalid</error>';
                $migrateState = $storageValueIsValid ? '' : '<error>unable to migrate</error>';

                if ($defaultValueIsValid && !$storageValueIsValid) {
                    $migrateValue = $predefinedKey->migrate($storageValue);
                    if (!$migrateValueIsValid = $predefinedKey->isValid($migrateValue, $message)) {
                        $output->writeln(sprintf('<error>%s</error> - %s - validation error on migrated value', $message, $key));
                    }

                    if ($migrateValueIsValid) {
                        if ($input->getOption('force')) {
                            $storageManager->saveValue($key, $migrateValue);
                            $doctrine->getEntityManager()->flush();
                            $storageState = strip_tags($storageState);
                            $migrateState = 'migrated';
                        } else {
                            $migrateState = 'use --force to migrate';
                        }
                    }
                }

                $table->addRow([$key, $defaultState, $storageState, $migrateState]);
            }
        }

        $table->render();
    }
}
