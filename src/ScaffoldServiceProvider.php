<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/10/18 03:33 PM
 */

namespace Rzian\Scaffold;

use Illuminate\Support\ServiceProvider;
use Rzian\Scaffold\Console\Commands\ScaffoldGenerator;
use Rzian\Scaffold\Console\Commands\ModelGenerator;
use Rzian\Scaffold\Console\Commands\ViewGenerator;
use Rzian\Scaffold\Console\Commands\ControllerGenerator;
use Rzian\Scaffold\Console\Commands\RouteGenerator;

class ScaffoldServiceProvider extends ServiceProvider
{	
    /**
     * Register command
     *
     * @return void
     */
    public function register()
    {
    	if ($this->app->runningInConsole()) {
	    	$this->commands([
				ScaffoldGenerator::class,
				ModelGenerator::class,
				ViewGenerator::class,
				ControllerGenerator::class,
				RouteGenerator::class
	        ]);
	    }
    }
}