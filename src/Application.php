<?php

namespace Console;

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

        $this->add(new TestCommand());
    }

    //@TODO check for configuration in project_root
    //@TODO implement auto-register method
    //@TODO define base commands that are common for more then one project
}
