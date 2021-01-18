<?php

declare(strict_types=1);

namespace ProdSpace\Console;

use ProdSpace\Console\Command\Ip\LanCommand;
use ProdSpace\Console\Command\Ip\WebCommand;
use ProdSpace\Console\Command\String\RandomCommand;
use ProdSpace\Console\Command\TestCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Yaml;

class CommandRegistry
{
    /**
     * @param Application $application
     * @throws \JsonException
     */
    public static function register(Application $application): void
    {
        self::registerBundledCommands($application);
        //self::registerAppCommands($application);
    }

    /**
     * This method registers commands that are bundled with this console application.
     *
     * @param Application $application
     * @throws \Exception
     */
    private static function registerBundledCommands(Application $application): void
    {
        if ($commandInstances = self::loadCommandInstances(__DIR__ . '/Command')) {
            self::registerCommandInstances($application, $commandInstances);
        }
    }

//    private static function registerAppCommands(Application $application): void
//    {
//        $composerBasedNameSpaces = self::getComposerNamespaces();
//
//        $composerFile = dirname(__DIR__, 4) . '/composer.json';
//
//        if (file_exists($composerFile)) {
//            $composerFileString = file_get_contents($composerFile);
//            $composerFileContent = json_decode($composerFileString, true, 512, JSON_THROW_ON_ERROR);
//
//            $psr0Namespaces = $composerFileContent['autoload']['psr-0'] ?? null;
//            $psr4Namespaces = $composerFileContent['autoload']['psr-4'] ?? null;
//
//            foreach ($psr4Namespaces as $namespace => $folder) {
//                if ($commandDirectory = self::getCommandDirectory($folder)) {
//                    $commandNamespace = sprintf('%sCommand', $namespace);
//                    $commandClasses = self::getCommandClassNames($commandDirectory, $commandNamespace);
//
//                    $commandInstances = self::getCommandInstances($commandClasses);
//                    foreach ($commandInstances as $commandInstance) {
//                        $application->add($commandInstance);
//                    }
//                }
//            }
//        }
//    }

    /**
     * @param string $directoryPath
     * @return array
     * @throws \Exception
     */
    private static function loadCommandInstances(string $directoryPath): ?array
    {
        if (file_exists($directoryPath) && is_dir($directoryPath)) {
            $directory = new \RecursiveDirectoryIterator(__DIR__ . '/Command');
            $iterator = new \RecursiveIteratorIterator($directory);
            $regex = new \RegexIterator($iterator, '/Command\.php/');

            return array_map(static function (string $commandFile) {
                $className = str_replace([__DIR__, '/', '.php'], [__NAMESPACE__, '\\', ''], $commandFile);
                return (new $className());
            }, iterator_to_array($regex));
        }

        return null;
    }

    /**
     * @param Application $application
     * @param array $commandInstances
     */
    private static function registerCommandInstances(Application $application, array $commandInstances): void
    {
        array_walk($commandInstances, static function (Command $command) use ($application) {
            $application->add($command);
        });
    }

    /**
     * @return array|null
     * @throws \JsonException
     */
    private static function getComposerNamespaces(): ?array
    {
        $composerFile = dirname(__DIR__, 4) . '/composer.json';

        if (file_exists($composerFile)) {
            $composerFileString = file_get_contents($composerFile);
            $composerFileContent = json_decode($composerFileString, true, 512, JSON_THROW_ON_ERROR);

            $psr0Namespaces = $composerFileContent['autoload']['psr-0'] ?? [];
            $psr4Namespaces = $composerFileContent['autoload']['psr-4'] ?? [];

            if (!empty($psr0Namespaces) || !empty($psr4Namespaces)) {
                return array_merge($psr0Namespaces, $psr4Namespaces);
            }
        }

        return null;
    }

    /**
     * @param string $folder
     * @return string
     */
    private static function getCommandDirectory(string $folder): ?string
    {
        $projectRoot = dirname(__DIR__, 4);
        $trimmedFolder = trim($folder, '/');
        $commandDirectory = sprintf('%s/%s/Command', $projectRoot, $trimmedFolder);

        if (file_exists($commandDirectory) && is_dir($commandDirectory)) {
            return $commandDirectory;
        }

        return null;
    }

}
