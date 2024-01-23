<?php

namespace Zicht\Bundle\KeyValueBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class KeyValueCreateDirectoryCommand extends Command
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    /** @var string */
    protected static $defaultName = 'zicht:key-value:create-directory';

    protected function configure(): void
    {
        $this
            ->setDefinition([new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web')])
            ->setDescription('Ensure that the `web/media/key_value_bundle` exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetArg = rtrim($input->getArgument('target'), '/');
        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        $this->filesystem->mkdir($targetArg . '/media/key_value_bundle/', 0777);

        return 0;
    }
}
