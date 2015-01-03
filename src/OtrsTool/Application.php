<?php


namespace OtrsTool;

use OtrsTool\Command\BuildOpmCommand;
use OtrsTool\Command\BuildOpmFromGitCommand;
use Symfony\Component\Console\Application as BaseApplication;


/**
 * The console application that handles the commands
 *
 */
class Application extends BaseApplication
{

    public function __construct()
    {
        if (function_exists('ini_set') && extension_loaded('xdebug')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);
        }

        parent::__construct('OtrsTools');
    }

    /**
     * Initializes all commands
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new BuildOpmCommand();
        $commands[] = new BuildOpmFromGitCommand();
        return $commands;
    }
}
