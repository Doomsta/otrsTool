<?php

namespace OtrsTool\Command\Repo;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildIndexCommand extends Command
{
    protected function configure()
    {
        $this->setName('repo:buildIndex');
        $this->addArgument(
            'dir',
            InputArgument::OPTIONAL,
            'dir where the index will be build',
            './'
        );
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception('#TODO');
    }
}
