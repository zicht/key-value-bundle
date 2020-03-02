<?php
declare(strict_types=1);
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManagerInterface;

class ListCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'zicht:key-value:list';

    /** @var KeyValueStorageManagerInterface|KeyValueStorageManager */
    private $storageManager;

    public function __construct(KeyValueStorageManagerInterface $storageManager, string $name = null)
    {
        parent::__construct($name);
        $this->storageManager = $storageManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('An overview of all keys in this project');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);
        $keys = $this->storageManager->getAllKeys();
        sort($keys);
        foreach ($keys as $key) {
            $table
                ->addRow([$key, $this->getReadableValue($this->storageManager->getValue($key))])
                ->addRow(new TableSeparator());
        }
        $table->render();
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    private function getReadableValue($value): ?string
    {
        return var_export($value, true);
    }
}
