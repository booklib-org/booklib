<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class CleanupImportDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-import-directory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = '/import';

        $finder = new Finder();
        $finder->files()
            ->in($directory);

        $extensions = [];
        foreach($finder as $file) {

            //Count the number of files for each extension
            if(!isset($extensions[$file->getExtension()])){
                $extensions[$file->getExtension()] = 1;
            } else {
                $extensions[$file->getExtension()]++;
            }

            //if the .epub file does not exist, delete the .bmf file
            if($file->getExtension() == 'bmf'){
                if(!file_exists(str_replace('.bmf', '', $file->getRealPath()))){
                    echo "Removing " . $file->getRealPath() . "\n";
                    unlink($file->getRealPath());
                }
            }

        }

        print_r($extensions);

        unset($finder);
        $finder = new Finder();
        $finder->directories()
            ->in($directory);

        $removeDirectories = [];
        foreach($finder as $directory) {

            $finder2 = new Finder();

            if(!$finder2->in($directory->getRealPath())->hasResults()){
                $removeDirectories[] = $directory->getRealPath();
            }

            unset($finder2);
        }

        foreach($removeDirectories as $directory){
            echo "Removing " . $directory . "\n";
            rmdir($directory);
        }
    }
}
