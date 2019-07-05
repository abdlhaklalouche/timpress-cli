<?php

namespace TimpressCLI;

use ZipArchive;

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
        
		$question = new Question("Name of your theme [<info>Timpress</info>]: ", null);
        $this->informations['theme-name'] = $helper->ask($input, $output, $question);
        
		$question = new Question("PSR-4 Namespace of your theme [<info>Timpress</info>]: ", null);
		$this->informations['theme-namespace'] = $helper->ask($input, $output, $question);
    
        $question = new Question("Theme URI [<info>Website URL for theme</info>]: ", null);
		$this->informations['theme-uri'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Description [<info>WordPress starter theme with timber support</info>]: ", null);
		$this->informations['theme-description'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Author name [<info>Author</info>]: ", null);
		$this->informations['theme-author'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Author URI [<info>Author website URL</info>]: ", null);
		$this->informations['theme-author-uri'] = $helper->ask($input, $output, $question);
        
        $question = new Question("Version [<info>1.0.0</info>]: ", null);
		$this->informations['theme-version'] = $helper->ask($input, $output, $question);

        $question = new Question("License [<info>GPL-3.0</info>]: ", null);
		$this->informations['theme-license'] = $helper->ask($input, $output, $question);
		
        $output->writeln('<info>Downloading theme..</info>');
        $zipfile = $this->download();
        
        $output->writeln('<info>Extracting theme files..</info>');
		$this->extract($zipfile, $directory, $output);
        
        $output->writeln('<info>Cleaning up..</info>');
        $this->cleanUp($zipfile);
        
        $output->writeln('<info>Intializing theme files..</info>');
        $this->init($directory, $output);
                        
		// $output->writeln('<info>Installing composer dependencies..</info>');
        // shell_exec("cd ".$directory." && composer install");
        
		// $output->writeln('<info>Installing npm dependencies..</info>');
		// shell_exec("cd ".$directory." && npm install");

        $output->writeln('<comment>Your theme is ready in (' . $directory . ')</comment>');
        $output->writeln('<fg=black;bg=green;options=bold>Happy developing :)</>');
    }

    /**
     * Downloading the Timpress theme from github repository from the master barnch.
     * 
     * @return string
     */
    private function download()
    {
        $zipfile = getcwd().'/timpress_' . uniqid() . '.zip';
        
        file_put_contents($zipfile, 
            file_get_contents($this->TIMPRESS_REPOPSITORY_URI)
        );

        return $zipfile;
    }

	/**
	 * Extract the downloaded zip file
	 *
	 * @param string $zipfile
	 * @param string $directory
	 * @param OutputInterface $output
	 * @return void
	 */
	private function extract($zipfile, $directory, OutputInterface $output)
	{
		$archive = new ZipArchive();
		$archive->open($zipfile);
		$archive->extractTo($directory);
        $archive->close();
        
		if (!empty(shell_exec("mv " . $directory . "/timpress-master/* " . $directory . "/timpress-master/.[!.]*  " . $directory))) {
			$output->writeln('<error>Cannot move files from timpress-master folder</error>');
			return $this;
		}
        
        @rmdir($directory . "/timpress-master");
        
        return $this;
    }
    
    /**
     * Remove the downloade zip file
     * 
     * @return void
     */
    private function cleanUp($zipfile)
	{
		@chmod($zipfile, 0777);
		@unlink($zipfile);
    }
    
    /**
     * Initializing and renaming files content
     * 
	 * @param string $directory
	 * @param OutputInterface $output
     * @return void
     */
    private function init($directory, OutputInterface $output)
    {
        foreach($this->getAllFiles($directory) as $file) {
            $str = file_get_contents($file);

            if (!is_null($this->informations['theme-name'])) {
                $str = str_replace("timpress", $this->informations['theme-name'], $str);
                $str = str_replace("Timpress", $this->informations['theme-name'], $str);
            }

            if (!is_null($this->informations['theme-namespace'])) {
                $str = str_replace("Timpress", $this->informations['theme-namespace'], $str);
            }
            
            if (!is_null($this->informations['theme-uri'])) {
                $str = str_replace("https://github.com/abdlhaklalouche/timpress", $this->informations['theme-uri'], $str);
            }
            
            if (!is_null($this->informations['theme-description'])) {
                $str = str_replace("WordPress starter theme with timber support", $this->informations['theme-description'], $str);
			}
            
            if (!is_null($this->informations['theme-author'])) {
                $str = str_replace("Abdelhak Lallouche", $this->informations['theme-author'], $str);
                $str = str_replace("abdlhaklalouche", $this->informations['theme-author'], $str);
			}
            
            if (!is_null($this->informations['theme-author-uri'])) {
                $str = str_replace("abdlhaklalouche@gmail.com", $this->informations['theme-author-uri'], $str);
                $str = str_replace("abdlhaklalouche@gmail.com", $this->informations['theme-author-uri'], $str);
			}
            
            if (!is_null($this->informations['theme-version'])) {
                $str = str_replace("1.0.0", $this->informations['theme-version'], $str);
            }
            
            if (!is_null($this->informations['theme-license'])) {
                $str = str_replace("GPL-3.0", $this->informations['theme-license'], $str);
                $str = str_replace("gpl-3.0", $this->informations['theme-license'], $str);
            }
            file_put_contents($file, $str);
        }
    }

    private function getAllFiles($dir, &$results = [])
    {
        $files = scandir($dir);
    
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this->getAllFiles($path, $results);
            }
        }
    
        return $results;
    }
}