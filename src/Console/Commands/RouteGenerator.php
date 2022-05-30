<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/10/18 04:38 PM
 */

namespace Rzian\Scaffold\Console\Commands;

use ReflectionClass;

class RouteGenerator extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:route {controller : The name of the controller resource}
                            {method?* : The name of the method(s) in the controller to be included}
                            {--api : This will inform to write the routes into the api channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a resourceful routing path for a controller';

    /**
     * The list of method verb for router.
     *
     * @var array
     */
    protected $verbs = ['get', 'post', 'put', 'patch', 'delete', 'options'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $name = $this->argument('controller');
        if (! class_exists($class = 'App\\Http\\Controllers\\'.$name))
        {
            $this->abort(sprintf(static::ERR_UNDEFINED, 'Controller', $name));                
        } 

        $source = sprintf('%s.php', $this->option('api') ? 'api' : env('SCAFFOLD_ROUTE', 'web'));
        $path = base_path($this->cleanPath('routes')).DIRECTORY_SEPARATOR.$source;
        if (! file_exists($path))
        {
            $this->abort(sprintf(static::ERR_UNDEFINED, 'app/routes', $source)); 
        }

        $this->write($path, $this->getRoutes($class, $this->argument('method')));
        
        return $this->info(sprintf("Routes successfully added to 'app/routes/%s'", $source));
    }

    /**
     * Get the route list formatted.
     *
     * @return array
     */
    public function getRoutes($class, $methods = array())
    {                
        $routes = array();
        $controller = new ReflectionClass($class);        
        foreach($controller->getMethods() as $method)
        {
            if ($method->class !== $class) continue;
            if (! $method->isPublic() || $method->isStatic()) continue;
            if (strpos($method->name, '__') !== false) continue;
            if (! empty($methods) && ! in_array($method->name, $methods)) continue;                    
            if (! preg_match('/\@route ([\|a-z]+) ([\w\/\-\{\?\}]+)(?: (.+))?/i', $method->getDocComment(), $result))continue;
            
            array_shift($result);
            list($verb, $path, $options) = array_pad($result, 3, false);            
            $routes[] = $this->prepare(strtolower($verb), $path, sprintf('%s@%s', last(explode('\\', $method->class)), $method->name), $options);
        }

        return $routes;
    }

    /**
     * Prepare route format
     *
     * @return string
     */    
    public function prepare($method, $uri, $action)
    {
        $params = sprintf("'%s', '%s'", $uri, $action);
        if (! in_array($method, $this->verbs))
        {            
            if(strpos($method, '|') !== false)
            {                
                $params = sprintf("['%s'], %s", implode("','", explode('|', $method)), $params);
                $method = 'match';
            } 
            else {
                $params = str_replace($uri, $method, $params);        
                $method = 'any';
            }
        }

        return sprintf("Route::%s(%s);", $method, $params);
    }

    /**
     * Execute generating routes
     *
     * @return void()
     */
    public function write($path, $contents)
    {
        $name = str_replace('Controller', '', $this->argument('controller'));
        $content = sprintf("\n\n/**\n * {$name} routes\n */\n%s\n//*End of {$name} routes*/", implode("\n", $contents));

        if (! file_put_contents($path, $content, FILE_APPEND | LOCK_EX))
        {
            exit($this->error(static::ERR_WRITE_PERMISSION));
        }
    }
}
