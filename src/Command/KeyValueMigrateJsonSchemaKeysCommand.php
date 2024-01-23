<?php

namespace Zicht\Bundle\KeyValueBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManagerInterface;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\PredefinedJsonSchemaKey;

class KeyValueMigrateJsonSchemaKeysCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'zicht:key-value:migrate-json-schema-keys';

    private KeyValueStorageManagerInterface $storageManager;

    private EntityManagerInterface $entityManager;

    public function __construct(
        KeyValueStorageManagerInterface $keyValueStorageManager,
        EntityManagerInterface $entityManager
    ) {
        $this->storageManager = $keyValueStorageManager;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Validate all json schema keys and migrate when possible')
            ->addOption('replace-invalid', null, InputOption::VALUE_NONE, 'Replace stored value with default value when unable to migrate')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force migration in the database to occur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $table = new Table($output);
        $table->setHeaders(['Key', 'Default', 'Storage', 'Migrate']);

        foreach ($this->storageManager->getAllKeys() as $key) {
            $predefinedKey = $this->storageManager->getPredefinedKey($key);
            if ($predefinedKey instanceof PredefinedJsonSchemaKey) {
                $key = $predefinedKey->getKey();
                $defaultValue = $predefinedKey->getValue();
                if (!$defaultValueIsValid = $predefinedKey->isValid($defaultValue, $message)) {
                    $io->writeln(sprintf('%s - validation error on default value', $key));
                    $io->writeln($message);
                    $io->error(json_encode($defaultValue, JSON_PRETTY_PRINT));
                }
                $defaultState = $defaultValueIsValid ? 'ok' : '<error>invalid</error>';
                $storageValue = $this->storageManager->getValue($key);
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
                            $this->storageManager->saveValue($key, $migrateValue);
                            $this->entityManager->flush();
                            $storageState = strip_tags($storageState);
                            $migrateState = 'migrated';
                        } else {
                            $migrateState = 'use --force to migrate';
                        }
                    } else {
                        if ($input->getOption('replace-invalid')) {
                            if ($input->getOption('force')) {
                                $this->storageManager->saveValue($key, $defaultValue);
                                $this->entityManager->flush();
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

        return 0;
    }
}
