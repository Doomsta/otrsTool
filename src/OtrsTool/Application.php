<?php


namespace OtrsTool;

use OtrsTool\Command\Opm\BuildFromGitCommand;
use OtrsTool\Command\Opm\BuildCommand;
use OtrsTool\Command\Package\CheckCommand;
use OtrsTool\Command\Package\CompareWithOtrsCommand;
use OtrsTool\Command\Repo\BuildIndexCommand;
use OtrsTool\Command\Repo\ServeCommand;
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
        $commands[] = new BuildCommand();
        $commands[] = new BuildFromGitCommand();
        $commands[] = new CheckCommand();
        $commands[] = new CompareWithOtrsCommand();
        $commands[] = new ServeCommand();
        $commands[] = new BuildIndexCommand();
        return $commands;
    }
}
