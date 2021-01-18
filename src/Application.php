<?php

namespace ProdSpace\Console;

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
        CommandRegistry::register($this);
    }

}
