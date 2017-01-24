<?php

namespace Fuguevit\NHDownloader\Command;

use Fuguevit\NHDownloader\Downloader;
use Fuguevit\NHDownloader\Exception\GuzzleResultCodeError;
use Fuguevit\NHDownloader\Helper\NHZipArchive;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Download extends Command
{
    public function configure()
    {
        $this->setName('download')
             ->setDescription('nhentai manga downloader')
             ->addArgument('id', InputArgument::REQUIRED, 'Manga Id')
             ->addOption('proxy', null, InputOption::VALUE_OPTIONAL)
             ->addOption('archive', null, InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');

        $this->startDownload($id, $input, $output)
            ->checkCompress($input->getOption('archive'), $id);

        $output->writeln('Download Success!');
    }

    protected function startDownload($id, InputInterface $input, OutputInterface $output)
    {
        try {
            $downloader = new Downloader($id, $input->getOption('proxy'));
            $downloader->start();
        } catch (GuzzleResultCodeError $exception) {
            $output->writeln($exception->getMessage());
        }

        return $this;
    }

    protected function checkCompress($flag, $id)
    {
        if (!$flag) {
            return;
        }

        $zipFile = __DIR__.'/../../storage/'.$id.'.zip';
        $dirLocation = __DIR__.'/../../storage/'.$id;

        NHZipArchive::zipFolder($dirLocation, $zipFile);
    }
}
