<?php

namespace Console;

use Composer\Autoload\ClassLoader;
use Console\Command\TestCommand;

use Symfony\Component\Console\Application as ConsoleApplication;

/**
 * Class Application
 * @package Console
 */
class Application extends ConsoleApplication
{
    /**
     * Application constructor.
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->registerCommandsByComposer();
        $this->add(new TestCommand());

        //Built-In Commands
        //get your-ip
        //calc rem
        //ssh helper
        //boilerplate
        //curl
        //password gen
        //md2pdf
        //hex2rgba
        //lint
        //translate
        //wiki
        //test data
        //link shortener
        //security
        //placeholder images
        //cloud storage
        //currency exchange
        //validations
        //bored
        //prettyfier
        //licence
        //postman
        //qrcode
        //quickchart
        //public apis
        //file.io
        //waka.time
        //deepcode
        //time
    }

    /**
     * @return array
     */
    private function registerCommandsByComposer(): array
    {
        $composerFile = dirname(__DIR__, 4) . '/composer.json';

        if (file_exists($composerFile)) {
            $composerFileString = file_get_contents($composerFile);
            $composerFileContent = json_decode($composerFileString, true);

            $psr0Namespaces = $composerFileContent['autoload']['psr-0'] ?? null;
            $psr4Namespaces = $composerFileContent['autoload']['psr-4'] ?? null;

            foreach ($psr4Namespaces as $namespace => $folder) {
                if ($commandDirectory = self::getCommandDirectory($folder)) {
                    $commandNamespace = sprintf('%sCommand', $namespace);
                    $commandClasses = self::getCommandClassNames($commandDirectory, $commandNamespace);

                    $commandInstances = $this->getCommandInstances($commandClasses);
                    foreach ($commandInstances as $commandInstance) {
                        $this->add($commandInstance);
                    }
                }
            }
        }

        return [];
    }

    private function getCommandInstances(array $fullNamespaces): array
    {
        return array_map(function ($fullNamespace) {
            if (class_exists($fullNamespace)) {
                return call_user_func_array([new \ReflectionClass($fullNamespace), 'newInstance'], []);
            }
        }, $fullNamespaces);
    }

    /**
     * @param string $folder
     * @return string
     */
    private static function getCommandDirectory(string $folder): ?string
    {
        $projectRoot = self::getProjectRoot();
        $trimmedFolder = trim($folder, '/');
        $commandDirectory = sprintf('%s/%s/Command', $projectRoot, $trimmedFolder);

        if (file_exists($commandDirectory) && is_dir($commandDirectory)) {
            return $commandDirectory;
        }

        return null;
    }

    /**
     * @param string $commandDirectory
     * @return array
     */
    private static function getCommandClassNames(string $commandDirectory, string $commandNamespace): array
    {
        return array_reduce(scandir($commandDirectory), static function (array $carry, string $item) use ($commandNamespace): array {
            if (preg_match('/(.*)Command.php/', $item, $matches)) {
                $commandClass = sprintf('%sCommand', $matches[1]);
                $carry[] = sprintf('%s\%s', $commandNamespace, $commandClass);
            }

            return $carry;
        }, []);
    }

    /**
     * @return string
     */
    private static function getProjectRoot(): string
    {
        return dirname(__DIR__, 4);
    }
    //@TODO check for configuration in project_root
    //@TODO implement auto-register method
    //@TODO define base commands that are common for more then one project
}
