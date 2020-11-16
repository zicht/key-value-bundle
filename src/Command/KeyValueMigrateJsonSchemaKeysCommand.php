<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zicht\Bundle\FrameworkExtraBundle\JsonSchema\SchemaService;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedJsonSchemaKey;

class KeyValueMigrateJsonSchemaKeysCommand extends ContainerAwareCommand
{
    /** @var string */
    protected static $defaultName = 'zicht:key-value:migrate-json-schema-keys';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Validate all json schema keys and migrate when possible')
            ->addOption('replace-invalid', null, InputOption::VALUE_NONE, 'Replace stored value with default value when unable to migrate')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force migration in the database to occur');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $table = new Table($output);
        $table->setHeaders(['Key', 'Default', 'Storage', 'Migrate']);

        /** @var KeyValueStorageManager $storageManager */
        $storageManager = $this->getContainer()->get('zicht_bundle_key_value.key_value_storage_manager');
        $doctrine = $this->getContainer()->get('doctrine');

        foreach ($storageManager->getAllKeys() as $key) {
            $predefinedKey = $storageManager->getPredefinedKey($key);
            if ($predefinedKey instanceof PredefinedJsonSchemaKey) {
                $key = $predefinedKey->getKey();
                $defaultValue = $predefinedKey->getValue();
                if (!$defaultValueIsValid = $predefinedKey->isValid($defaultValue, $message)) {
                    $io->writeln(sprintf('%s - validation error on default value', $key));
                    $io->writeln($message);
                    $io->error(json_encode($defaultValue, JSON_PRETTY_PRINT));
                }
                $defaultState = $defaultValueIsValid ? 'ok' : '<error>invalid</error>';
                $storageValue = $storageManager->getValue($key);
                if (!$storageValueIsValid = $predefinedKey->isValid($storageValue, $message)) {
                    $io->writeln(sprintf('%s - validation error on storage value', $key));
                    $io->writeln($message);
                    $io->error(json_encode($storageValue, JSON_PRETTY_PRINT));
                }
                $storageState = $storageValueIsValid ? 'ok' : '<error>invalid</error>';
                $migrateState = $storageValueIsValid ? '' : '<error>unable to migrate</error>';

                if ($defaultValueIsValid && !$storageValueIsValid) {
                    $migrateValue = $predefinedKey->migrate($storageValue);
                    if (!$migrateValueIsValid = $predefinedKey->isValid($migrateValue, $message)) {
                        $io->writeln(sprintf('%s - validation error on migrated value', $key));
                        $io->writeln($message);
                        $io->error(json_encode($migrateValue, JSON_PRETTY_PRINT));
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
                    } else {
                        if ($input->getOption('replace-invalid')) {
                            if ($input->getOption('force')) {
                                $storageManager->saveValue($key, $defaultValue);
                                $doctrine->getEntityManager()->flush();
                                $storageState = strip_tags($storageState);
                                $migrateState = 'replaced with default';
                            } else {
                                $migrateState = 'use --force to replace with default';
                            }
                        }
                    }
                }

                $table->addRow([$key, $defaultState, $storageState, $migrateState]);
            }
        }

        $table->render();
    }
}
