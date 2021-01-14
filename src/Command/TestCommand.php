<?php


namespace Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package Console\Command
 */
class TestCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('test:run')
            ->setDescription('Just a test command to verify that everything works.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Testrun finished!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('ERROR: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
