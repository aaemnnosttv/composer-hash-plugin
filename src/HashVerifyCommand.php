<?php

namespace ComposerHash;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HashVerifyCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('hash-verify');
        $this->setDescription('Verify the installed dependencies are in sync with the lock file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once __DIR__ . '/functions.php';
        try {
            verify(dirname($this->getComposer()->getConfig()->getConfigSource()->getName()));
            $output->writeln("<info>Hashes match.</info>");
        } catch (\Exception $exception) {
            $output->writeln('<warning>' . $exception->getMessage() . '</warning>');
            exit(1);
        }
    }
}
