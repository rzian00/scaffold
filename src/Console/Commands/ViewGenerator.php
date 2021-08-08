<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 2/20/18 11:50 AM
 */

namespace Rzian\Scaffold\Console\Commands;

class ViewGenerator extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:view
                            {model : The model class for generating view}
                            {name? : The name of the view resource. (Optional)}
                            {--scaffold=default : The path name of the scaffold resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a resourceful view for a model class';

    /**
     * The field type based to table schema data types.
     *
     * @var string
     */
    protected $inputFields = [
        'string' => '<input type="text" %s />',
        'text' => '<textarea %s></textarea>'
    ];


    /**
     * The default field type.
     *
     * @var string
     */
    protected $defaultField = 'string';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! class_exists($classmap = 'App\\'.$this->argument('model')))
        {
            $this->abort(sprintf(static::ERR_UNDEFINED, 'Model', $classmap));
        }

        $class = last(explode('\\', $classmap));
        $name = $this->argument('name') ?: lcfirst($class);
        $views = [];

        if ($path = $this->validate($this->cleanPath("{$name}/form.blade.php")))
        {                                    
            preg_match('/<g:input ([^>]*)>/', $field = $this->getContent('view.form.field'), $context);
            $views['form'] = [$path, 'fields', function($name, $type, $delimiters) use ($context, $field){
                return preg_replace($delimiters, [$name, $this->label($name)], str_replace($context[0], sprintf((array_key_exists($type, $this->inputFields) ? $this->inputFields[$type] : $this->inputFields[$this->defaultField]), $context[1]), $field));
            }];
        }

        if ($path = $this->validate($this->cleanPath("{$name}/index.blade.php")))
        {                             
            $field = $this->getContent('view.index.column');
            $views['index'] = [$path, 'columns', function($name, $type, $delimiters) use ($field){
                return preg_replace($delimiters, [$name, $this->label($name)], $field);
            }];
        }

        if (empty($views))
        {
            exit($this->info(static::INFO_ABORTED));
        }

        foreach($this->prepare($classmap, $views) as $name => $view) 
        {
            $this->generate("view.{$name}", $view->path, $view->params);
            $this->info(sprintf(static::INFO_SUCCESS, 'View', $name));
        }
    }

    /**
     * Execute the console command.
     *
     * @return array
     */
    protected function prepare($classmap, $views)
    {
        $table = $this->getTable($this->forceGetAttribute('table', new $classmap));
        $delimiters = [$this->delimiter('name'), $this->delimiter('label')];
        $route = $table->getName();
        $title = $this->label($route);
        $contexts = [];        
        foreach($views as $key => $view)
        {
            list($path, $mapping, $method) = $view;            
            $content = '';                   
            foreach($table->getColumns() as $name => $column)
            {                
                $content .= call_user_func_array($method, [$name, $column->getType()->getName(), $delimiters]);
            }            

            $contexts[$key] = (object)[
                'path' => $path,
                'params' => [
                    'route' => $route,
                    'title' => $title,
                    $mapping => $content
                ]
            ];
        }

        return $contexts;
    }

    /**
     * Validates the existence of the file
     *
     * @param string $filename
     * @return mixed
     */
    protected function validate($filename)
    {
        if (file_exists($path = $this->getViewPath().$filename)
            && ! $this->confirm(sprintf(static::INFO_OVERWRITE, 'View', $filename), true))
        {
            return false;
        }

        return $path;
    }
}