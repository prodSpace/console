<?php

declare(strict_types=1);

namespace ProdSpace\Console;

/**
 * Class CommandRegistry
 * @package ProdSpace\Console
 */
class CommandRegistry
{
    /**
     * @param Application $application
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public static function register(Application $application): void
    {
        self::registerBundledCommands($application);
        self::registerAppCommands($application);
    }

    /**
     * This method registers commands that are bundled with this console application.
     *
     * @param Application $application
     * @throws \ReflectionException
     */
    private static function registerBundledCommands(Application $application): void
    {
        if ($commandClassNames = self::loadCommandClassNames(__DIR__, __NAMESPACE__)) {
            self::registerCommandInstances($application, $commandClassNames);
        }
    }

    /**
     * @param Application $application
     * @throws \JsonException
     * @throws \ReflectionException
     */
    private static function registerAppCommands(Application $application): void
    {
        $composerBasedNameSpaces = self::getComposerSourceDirectories();

        foreach ($composerBasedNameSpaces as $namespace => $directory) {
            if ($commandClassNames = self::loadCommandClassNames($directory, $namespace)) {
                self::registerCommandInstances($application, $commandClassNames);
            }
        }
    }

    /**
     * @param string $directoryPath
     * @return array
     * @throws \Exception
     */
    private static function loadCommandClassNames(string $directoryPath, string $namespace): ?array
    {
        if (file_exists($directoryPath) && is_dir($directoryPath)) {
            $directory = new \RecursiveDirectoryIterator($directoryPath);
            $iterator = new \RecursiveIteratorIterator($directory);
            $regex = new \RegexIterator($iterator, '/Command\.php/');

            return array_map(static function (string $commandFile) use ($directoryPath, $namespace) {
                $trimmedNamespace = rtrim($namespace, '\\');
                return str_replace([$directoryPath, '/', '.php'], [$trimmedNamespace, '\\', ''], $commandFile);
            }, iterator_to_array($regex));
        }

        return null;
    }

    /**
     * @param Application $application
     * @param array $commandClassNames
     * @throws \ReflectionException
     */
    private static function registerCommandInstances(Application $application, array $commandClassNames): void
    {
        array_walk($commandClassNames, function (string $commandClass) use ($application) {
            if (class_exists($commandClass)) {
                $commandInstance = call_user_func_array([new \ReflectionClass($commandClass), 'newInstance'], []);
                $application->add($commandInstance);
            }
        });
    }

    /**
     * @return array|null
     * @throws \JsonException
     */
    private static function getComposerSourceDirectories(): ?array
    {
        $composerFile = dirname(__DIR__, 4) . '/composer.json';

        if (file_exists($composerFile)) {
            $composerFileString = file_get_contents($composerFile);
            $composerFileContent = json_decode($composerFileString, true, 512, JSON_THROW_ON_ERROR);

            $psr0Namespaces = $composerFileContent['autoload']['psr-0'] ?? [];
            $psr4Namespaces = $composerFileContent['autoload']['psr-4'] ?? [];

            if (!empty($psr0Namespaces) || !empty($psr4Namespaces)) {
                return array_map(static function (string $folder) {
                    $projectRoot = dirname(__DIR__, 4);
                    $trimmedFolder = trim($folder, '/');
                    return sprintf('%s/%s', $projectRoot, $trimmedFolder);
                }, array_merge($psr0Namespaces, $psr4Namespaces));
            }
        }

        return null;
    }

}
