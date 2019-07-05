<?php

namespace TimpressCLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class GenerateCommand extends Command
{
    protected static $defaultName = 'new';

    protected function configure()
    {
		$this
			->setDescription('Create a new Timpress theme')
			->addArgument('name', InputArgument::REQUIRED, 'Insert the folder name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
    }
}