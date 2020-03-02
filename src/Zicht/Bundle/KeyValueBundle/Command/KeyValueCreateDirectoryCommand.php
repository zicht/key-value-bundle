<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class KeyValueCreateDirectoryCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'zicht:key-value:create-directory';

    /** @var Filesystem */
    private $fileSystem;

    public function __construct(Filesystem $filesystem, string $name = null)
    {
        parent::__construct($name);
        $this->fileSystem = $filesystem;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                [
                    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
                ]
            )
            ->setDescription('Ensure that the `web/media/key_value_bundle` exists');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');
        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        $this->fileSystem->mkdir($targetArg . '/media/key_value_bundle/', 0777);
    }
}
