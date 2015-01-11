<?php

namespace OtrsTool\Command\Opm;

use OtrsTool\Exception\SopmNotFoundException;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class BuildCommand extends Command
{

    protected function configure()
    {
        $this->setName('opm:build');
        $this->addArgument(
            'source',
            InputArgument::OPTIONAL,
            'path to *.sopm e.g. /home/user/CustomColors',
            '.'
        );
        $this->addArgument(
            'target',
            InputArgument::OPTIONAL,
            'the target',
            sys_get_temp_dir()
        );
        $this->addOption('buildNumber', 'B', InputOption::VALUE_OPTIONAL, 'the buildNumber');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $buildNumber = $input->getOption('buildNumber');
        $target = $input->getArgument('target');

        $sopm = $this->getSopm($source);
        $output->writeln('found sopm');

        $sopm->BuildDate[0] = date('Y-m-d H:m:s');
        $sopm->BuildHost[0] = $sopm->BuildHost[0] ?: gethostname();
        $sopm->Version = ($buildNumber !== null)? $sopm->Version.'.'.$buildNumber : $sopm->Version;

        foreach($sopm->Filelist->File as $file) {
            /** @var $file SimpleXMLElement */
            $file['Encode'] = "Base64";
            $output->writeln('read otrs/'.$file['Location']);
            $file[0] = base64_encode(file_get_contents($source.'/otrs/'.$file['Location']));
        }
        $target .= '/'.$sopm->Name.'-'.$sopm->Version.'.opm';
        $output->writeln('try to write '. $target);
        if($sopm->saveXML($target)) {
            $output->writeln('success');
        } else {
            $output->writeln('failed!');
        }
    }

    protected function getSopm($path)
    {
        $finder = new Finder();
        $finder->name('*.sopm')->in($path);
        foreach ($finder as $file) {
            return new SimpleXMLElement(file_get_contents($file->getRealPath()));
        }
        throw new SopmNotFoundException('unable to find a *.sopm file');
    }
}
