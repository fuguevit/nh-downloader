<?php

namespace Fuguevit\NHDownloader;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    public function configure()
    {
        $this->setName('nh')
             ->setDescription('nhentai manga downloader')
             ->addArgument('id', InputArgument::REQUIRED, 'Manga Id');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $message = 'Hello, You want to download manga : '.$input->getArgument('id').'? Really?';
        $output->writeln("<info>{$message}</info>");
    }
}
