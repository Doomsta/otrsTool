<?php

namespace OtrsTool\Command\Repo;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig_Loader_Filesystem;

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
        $this->addOption('Print', 'P', InputOption::VALUE_NONE, 'prints the sopm');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        $print = (bool) $input->getOption('Print');
        $opms = [];
        foreach((new Finder())->in($dir)->name('*.opm') as $file) {
            /** @var SplFileInfo $file */
            try {
                $opm = new \SimpleXMLElement($file->getContents());
                $opms[] = $this->getPackageData($opm, $file->getRelativePathName());
                
            } catch (\Exception $e) {
                $output->writeln('skipped!!');
            }

        }
        $index = (new \Twig_Environment(new Twig_Loader_Filesystem([__DIR__.'/../../Templates'])))
            ->render('otrs.xml.twig', ['opms' =>$opms]);
        if($print) {
            $output->writeln($index);
        } else {
            if(file_put_contents($dir.'/otrs.xml', $index)) {
                $output->writeln('wrote new otrs.xml with '.count($opms).' entries');
                return 0;
            } else {
                $output->writeln('failed!');
                return 1;
            }
        }
        return 1;
    }

    /**
     * @TODO return a opm obj
     * @param \SimpleXMLElement $opm
     * @param string $path
     * @return array
     */
    private function getPackageData(\SimpleXMLElement $opm, $path)
    {
        $data = [
            'name' => (string) $opm->Name,
            'version' => (string) $opm->Version,
            'url' => (string) $opm->URL,
            'vendor' => (string) $opm->Vendor,
            'license' => (string )$opm->License,
            'framework' =>(string) $opm->Framework,
            'path' => '/'.$path
        ];
        foreach($opm->Description as $desc) {

            $data['descs'][] = [
                'lang' => (string)$desc['Lang'],
                'str' => (string)$desc,
            ];
        }
        return $data;
    }

}
