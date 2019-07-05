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

    protected $informations = [];

    protected $TIMPRESS_REPOPSITORY_URI = 'https://github.com/abdlhaklalouche/timpress/archive/master.zip';

    protected function configure()
    {
		$this
			->setDescription('Create a new Timpress theme')
			->addArgument('name', InputArgument::REQUIRED, 'Insert the folder name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$folderName = $input->getArgument('name');
		$directory = getcwd() . '/' . $folderName;
        $helper = $this->getHelper("question");
        
		$question = new Question("Name of your theme [<info>tipress</info>]: ", null);
        $this->informations['theme-name'] = $helper->ask($input, $output, $question);
        
		$question = new Question("PSR-4 Namespace of your theme [<info>Timpress</info>]: ", null);
		$this->informations['theme-namespace'] = $helper->ask($input, $output, $question);
    
        $question = new Question("Theme URI [<info>Website URL for theme</info>]: ", null);
		$this->informations['theme-uri'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Description [<info>Timpress a wordpress starter theme</info>]: ", null);
		$this->informations['theme-description'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Author name [<info>Author</info>]: ", null);
		$this->informations['theme-author'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Author URI [<info>Author website URL</info>]: ", null);
		$this->informations['theme-autor-uri'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Version [<info>1.0.0</info>]: ", null);
		$this->informations['theme-version'] = $helper->ask($input, $output, $question);

        $question = new Question("License [<info>MIT</info>]: ", null);
		$this->informations['theme-license'] = $helper->ask($input, $output, $question);
		
        $output->writeln('<info>Downloading theme..</info>');
        //Download here
        
        $output->writeln('<info>Extracting theme files..</info>');
		// Extract here
        
        $output->writeln('<info>Intializing theme files..</info>');
        // Rename here
                        
		$output->writeln('<info>Installing composer dependencies..</info>');
        shell_exec("cd ".$directory." && composer install");
        
		$output->writeln('<info>Installing npm dependencies..</info>');
		shell_exec("cd ".$directory." && npm install");

        $output->writeln('<comment>Your theme is ready in (' . $directory . ')</comment>');
        $output->writeln('<fg=black;bg=green;options=bold>Happy developing :)</>');
    }
}