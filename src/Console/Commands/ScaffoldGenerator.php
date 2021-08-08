<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/7/18 12:17 AM
 */

namespace Rzian\Scaffold\Console\Commands;

use Illuminate\Support\Facades\File;

class ScaffoldGenerator extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:scaffold {name=default : The name of the folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a scaffold template';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        if (file_exists($scaffold = $this->getUserScaffoldPath().$name))
        {
            if (! $this->confirm(sprintf(static::INFO_OVERWRITE, 'Scaffold', $name), true))
            {
                return $this->info(static::INFO_ABORTED);
            }

            if (! File::deleteDirectory($scaffold))
            {
                $this->abort(static::ERR_WRITE_PERMISSION);
            }
        }

        if(! File::copyDirectory($this->getScaffoldPath(), $scaffold))
        {
            $this->abort(static::ERR_WRITE_PERMISSION);
        }

        return $this->info(sprintf(static::INFO_SUCCESS, 'Scaffold', $name));
    }
}