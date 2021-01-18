<?php


namespace ProdSpace\Console\Command\Ip;

use ProdSpace\Console\Base\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IPWebCommand
 * @package Console\Command
 */
class WebCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('ip:web')
            ->setDescription('This command returns your current IP.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $client = Client::getInstance();
            $response = $client->request('GET', 'https://api.ipify.org?format=json');

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Cannot resolve request to IP api.');
            }

            $content = $response->toArray();

            if (!array_key_exists('ip', $content)) {
                throw new \Exception('Cannot resolve you IP.');
            }

            $output->writeln(sprintf('Your IP is: %s', $content['ip']));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('ERROR: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
