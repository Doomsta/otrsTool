<?php

namespace OtrsTool\Command\Package;


use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CompareWithOtrsCommand extends Command
{
    protected function configure()
    {
        $this->setName('package:compare');
        $this->addArgument(
            'packageDir',
            InputArgument::REQUIRED,
            'path to *.sopm e.g. /home/user/CustomColors'
        ); 
        $this->addArgument(
            'otrsDir',
            InputArgument::REQUIRED,
            'path to otrs root'
        );
        
        $this->addOption('diff', 'd', InputOption::VALUE_NONE, 'display diff');
        $this->addOption('same', 's', InputOption::VALUE_NONE, 'hide same file');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageDir = $input->getArgument('packageDir');
        $otrsDir = $input->getArgument('otrsDir');
        $displayDiff = $input->getOption('diff');
        $hideSame = $input->getOption('same');
        
        $output->writeln('start comparing');

        $packageFiles = array();
        foreach ((new Finder())->in($packageDir.'/otrs')->files() as $file) {
            /** @var SplFileInfo $file */
            $packageFiles[] = $file->getRelativePathname();
        }
        foreach($packageFiles as $file) {
            if(!file_exists($otrsDir.'/'.$file)) {
                if(!$hideSame) {
                    $output->writeln('<info>new</info> '.$file);
                }
                continue;
            }
            $otrsFileContent = file_get_contents($otrsDir.'/'.$file);
            $packageFileContent = file_get_contents($otrsDir.'/'.$file);
            if($otrsFileContent === $packageFileContent) {
                $output->writeln('<info>same</info> '.$file);   
            } else {
                $output->writeln('<info>different</info> '.$file);
                if($displayDiff) {
                    $differ = new Differ();
                    $output->writeln($differ->diff($otrsFileContent, $packageFileContent));
                }
            }
        }
    }
}