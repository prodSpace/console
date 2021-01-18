<?php

declare(strict_types=1);

namespace ProdSpace\Console\Command\String;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RandomCommand
 * @package ProdSpace\Console\Command\String
 */
class RandomCommand extends Command
{
    private const ALPHABET = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    private const DIGITS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    private const MISC_CHARS = ['-', '_', '!', '$', '%', '&', '*', '\\', '/', '\'', '"', '?', '.', '#', '~', '+', '§', '='];
    private const BRACKETS = ['[', ']', '(', ')', '{', '}', '<', '>'];

    protected function configure(): void
    {
        $this
            ->setName('string:random')
            ->setDescription('Generates a random string!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $strLength = 26;
            $strCharset = self::getRandomizedBaseCharset();
            $randomString = '';

            for($i = 0; $i <= $strLength; $i++) {
                $key = random_int(0, (count($strCharset) - 1));
                $randomString .= $strCharset[$key];
            }

            $output->writeln($randomString);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('ERROR: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }

    /**
     * @return array
     */
    private static function getRandomizedBaseCharset(): array
    {
        $charset = array_merge(
            self::ALPHABET,
            array_map('strtoupper', self::ALPHABET),
            self::DIGITS,
            self::MISC_CHARS,
            self::BRACKETS
        );

        shuffle($charset);

        return $charset;
    }
}
