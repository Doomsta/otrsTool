<?php

namespace OtrsTool\Command\Package;

use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CheckCommand extends Command
{

    protected function configure()
    {
        $this->setName('package:check');
        $this->addArgument(
            'packageDir',
            InputArgument::OPTIONAL,
            'path to *.sopm e.g. /home/user/CustomColors',
            './'
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('packageDir');
        $output->writeln('start package check');
        $output->writeln('dir: ' . $dir);
        try {
            $sopm = $this->findSopm($dir, $output);
        } catch (\Exception $e) {
            return 0;
        }
        if(!$this->checkFiles($sopm, $dir, $output)) {
            return 0;
        }
        return 1;

    }

    /**
     * @param $path
     * @param OutputInterface $output
     * @return SimpleXMLElement
     * @throws \Exception
     */
    protected function findSopm($path, OutputInterface $output)
    {
        $sopms = array();
        foreach ((new Finder())->name('*.sopm')->in($path) as $file) {
            /** @var SplFileInfo $file */
            $sopms[] = $file->getRealPath();
            $output->writeln('found ' . $file->getRelativePathname());
        }
        if (count($sopms) !== 1) {
            $output->writeln('Found ' . count($sopms) . ' sopms.');
            $output->writeln('there MUST be (only) one!');
            throw new \Exception();
        }

        return new SimpleXMLElement(file_get_contents($sopms[0]));
    }

    protected function checkFiles($sopm, $dir, OutputInterface $output)
    {
        $sopmFiles = array();
        foreach ($sopm->Filelist->File as $file) {
            $sopmFiles[] = (string)$file['Location'];
        }
        sort($sopmFiles);
        $otrsDirFiles = array();
        foreach ((new Finder())->files()->in($dir . '/otrs') as $file) {
            /** @var SplFileInfo $file */
            $otrsDirFiles[] = $file->getRelativePathname();
        }
        sort($otrsDirFiles);
        if($sopmFiles === $otrsDirFiles) {
            $output->writeln('<info>sopm filelist is in sync with otrs/</info>');
            return true;
        }
        $output->writeln('<error>.sopm filelist isnt in sync with otrs/</error>');
        if($diff = array_diff($sopmFiles,$otrsDirFiles )) {
            $output->writeln($diff);
            $output->writeln('are in sopm filelist but not in otrs/');
            return false;
        }
        if($diff  = array_diff($otrsDirFiles, $sopmFiles)) {
            $output->writeln($diff);
            $output->writeln('are in otrs/ but not in sopm filelist');
            return false;
        }
    }
}
