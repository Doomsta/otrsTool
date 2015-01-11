<?php

namespace OtrsTool\Command\Repo;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    protected function configure()
    {
        $this->setName('repo:serve');
        $this->addArgument(
            'dir',
            InputArgument::OPTIONAL,
            'dir to serve',
            './'
        );
        $this->addOption('watch', 'w', InputOption::VALUE_NONE, 'watch an rebuild the index');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception('#TODO');
    }
}