<?php

namespace OtrsTool\Command\Opm;

use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildFromGitCommand extends Command
{

    protected function configure()
    {
        $this->setName('opm:buildFromGit');
        $this->addArgument(
            'source',
            InputArgument::REQUIRED,
            'the git ssh clone url'
        );
        $this->addArgument(
            'target',
            InputArgument::OPTIONAL,
            'the target'
        );
        $this->addArgument(
            'workingDir',
            InputArgument::OPTIONAL,
            'the tmp to work with the code',
            sys_get_temp_dir()
        );
        $this->addOption(
            'branch',
            'b',
            InputOption::VALUE_OPTIONAL,
            'the branch'
        );
        $this->addOption('buildNumber', 'B', InputOption::VALUE_OPTIONAL, 'the buildNumber', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');
        $workDir = $input->getArgument('workingDir');
        $buildNumber = $input->getOption('buildNumber');

        $returnCode = 1;
        $key = 'otrsTool:'.uniqid();
        $git = new Git();

        $output->writeln('clone source to '.$workDir.'/'.$key);
        $git->clone($source, $workDir.'/'.$key);
        $output->writeln('clone done');
        $git->setRepository( $workDir.'/'.$key);
        if ($input->hasOption('branch')) {
            $output->writeln('git checkout '.$input->getOption('branch'));
            $git->checkout($input->getOption('branch'));
        }

        try {
            $command = $this->getApplication()->find('opm:build');
            $arguments = array(
                'source' => $workDir.'/'.$key,
                'target' => $target,
                '--buildNumber'  => $buildNumber,
            );

            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        } catch(\Exception $e) {
            $output->writeln('Error: '.$e->getMessage());
        }
        $output->writeln('remove tmp dir');
        (new Filesystem())->remove($workDir.'/'.$key);
        return $returnCode;
    }

}