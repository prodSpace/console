<?php


namespace ProdSpace\Console\Command\Ip;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IPLocalCommand
 * @package Console\Command
 */
class LanCommand extends Command
{
    protected function configure(): void
    {
        //@TODO make ethernet adapter selectable
        $this
            ->setName('ip:lan')
            ->setDescription('This command returns your current local IP.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $localIP = getHostByName(getHostName());
            $output->writeln(sprintf('Your IP is: %s', $localIP));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('ERROR: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
