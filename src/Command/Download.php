<?php

namespace Fuguevit\NHDownloader\Command;

use Fuguevit\NHDownloader\Downloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Download extends Command
{
    public function configure()
    {
        $this->setName('download')
             ->setDescription('nhentai manga downloader')
             ->addArgument('id', InputArgument::REQUIRED, 'Manga Id');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $downloader = new Downloader($input->getArgument('id'));
        $downloader->start();
    }
}
