<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/7/18 11:12 PM
 */

namespace Rzian\Scaffold\Console\Commands;

use Illuminate\Support\Str;

class ControllerGenerator extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:controller
                            {model : The model class resource.}
                            {--name= : The name of the controller.}
                            {--scaffold=default : The path name of the scaffold resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a controller class for a model with resourceful routes and contents';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! class_exists($classmap = 'App\\Models\\'.$this->argument('model')))
        {
            $this->abort(sprintf(static::ERR_UNDEFINED, 'Model', $classmap));
        }

        $class = last(explode('\\', $classmap));
        $varname = lcfirst($class);
        $name = $this->require('name') ?: sprintf('%sController', $class);
        $var = sprintf('$%s', $varname);
        $route = sprintf('/%s', Str::plural($varname));
        $this->generateRoutes($class, ['controller' => $name, 'varname' => $varname, 'route' => $route]);
        $this->generate('controller',
            $this->validate($name),
            compact('name','classmap','class','var','varname','route')
        );
        return $this->info(sprintf(static::INFO_SUCCESS, 'Controller', $name));
    }

    /**
     * Validates the existence of the file
     *
     * @param string $filename
     * @return mixed
     */
    protected function validate($filename)
    {
        if (file_exists($path = sprintf('%s.php', $this->getControllerPath().$filename))
            && ! $this->confirm(sprintf(static::INFO_OVERWRITE, 'Controller', $filename), true))
        {
            exit($this->info(static::INFO_ABORTED));
        }

        return $path;
    }

    protected function generateRoutes($name, $params)
    {
        $pattern = [];
        $replacements = [];

        foreach ($params as $key => $value)
        {
            $pattern[] = $this->delimiter($key);
            $replacements[] = $value;
        }

        $content = preg_replace($pattern, $replacements, $this->getContent('route'));
        $source = 'web.php';
        $path = base_path($this->cleanPath('routes')).DIRECTORY_SEPARATOR.$source;
        if (! file_exists($path))
        {
            $this->abort(sprintf(static::ERR_UNDEFINED, 'app/routes', $source));
        }

        $content = sprintf("\n\n/**\n * {$name} routes\n */\n%s\n//*End of {$name} routes*/", $content);

        if (! file_put_contents($path, $content, FILE_APPEND | LOCK_EX))
        {
            exit($this->error(static::ERR_WRITE_PERMISSION));
        }
    }
}
