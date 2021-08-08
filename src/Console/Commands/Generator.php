<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/6/18 11:25 PM
 */

namespace Rzian\Scaffold\Console\Commands;

use Rzian\Scaffold\Table;
use Illuminate\Console\Command;
use ReflectionProperty as Property;

abstract class Generator extends Command
{
    /**
     * Delimiter pattern
     */
    const DELIMITER = '/\@\{%s\}/';

	/**
     * Information message success
     */
    const INFO_SUCCESS = "...%s '%s' generated successfully!";

	/**
     * Information message abort
     */
    const INFO_ABORTED = '...Aborted';

	/**
     * Information message ask to overwrite
     */
	const INFO_OVERWRITE = "%s '%s' is already exists. Do you wish to overwrite?";

	/**
     * Error message write permission
     */
	const ERR_WRITE_PERMISSION = "Error: Unable to write to your application resource path. Please set the correct permission!";

	/**
     * Error message undefined context
     */
    const ERR_UNDEFINED = "Error: Undefined %s '%s'.";

	/**
     * Error message required option
     */
    const ERR_REQUIRED = 'Error: Option [--%s] is required when no assigned value!';    

	/**
	 * The scaffold generation
	 *
	 * @param string $scaffold
	 * @param string $path
	 * @param array $params
	 * @return void()
	 */
    public function generate($scaffold, $path, $params = array())
    {        
        $params = array_merge($params, $defaults = [
	        'user' => env('APP_DEV', gethostname()),
	        'datetime' => date('m/d/Y h:i A')
    	]);

        foreach ($params as $key => $value)
        {
            $pattern[] = $this->delimiter($key);
            $replacements[] = $value;
        }

        $content = preg_replace($pattern, $replacements, $this->getContent($scaffold));
        if (! file_exists($dir = dirname($path)))
        {
            @mkdir($dir, 0777, true);
        }

        if (! file_put_contents($path, $content))
        {
            exit($this->error(static::ERR_WRITE_PERMISSION));
        }
    }   

	/**
     * Converts the name to delimited key
     *
     * @param string $name
     * @return string
     */
    public function delimiter($name)
    {        
        return sprintf(self::DELIMITER, strtoupper($name));
    }

    /**
     * Parses column label name
     *
     * @param $name
     * @return string
     */
    protected function label($name)
    {
        return ucwords(str_replace('_', ' ', $name));   
    }

    /**
     * Get the required option but terminates session when value is empty string
     *
     * @param string $key
     * @param string $method
     * @return mixed
     */
    public function require($key, $method = 'option')
    {    	
    	$value = call_user_func([$this, $method], $key);
		if (! is_null($value) && empty($value))
        {
            $this->abort(sprintf(static::ERR_REQUIRED, $option));
        }
    	
    	return $value;
    }

    /**
     * Terminates the operation and leave a message
     *
     * @param string $message
     * @return void()
     */
    public function abort($message)
    {
    	$this->error($message);

    	exit($this->info(self::INFO_ABORTED));
    }

    /**
	 * Get the scaffold resource
	 *
	 * @param string $file
	 * @return string|void()
	 */
    public function getScaffold($file)
    {    
    	$file .= '.php';
        if(empty($name = $this->option('scaffold')))
        {            
            exit($this->error(sprintf(static::ERR_REQUIRED, 'Option scaffold')));
        }        

        if (! file_exists($scaffold = $this->getUserScaffoldPath().$name.DIRECTORY_SEPARATOR.$file))
        {
            if ($name !== 'default')
            {
                exit($this->error(sprintf(static::ERR_UNDEFINED, 'Scaffold', $name)));
            }

            return $this->getScaffoldPath().$file;
        }

        return $scaffold;
    }

    /**
     * Get the schema table manager
     *
     * @param string $name
     * @return \Rzian\Scaffold\Table|void()
     */
    public function getTable($name)
    {
		if (! Table::isExists($name))
        {
            $this->abort(sprintf(static::ERR_UNDEFINED, 'Table', $name));
        }

        return new Table($name);
    }

    /**
     * Get the content of the scaffold resource
     *
     * @param string $file
     * @return string
     */
    public function getContent($file)
    {
        return file_get_contents($this->getScaffold($file));
    }

	/**
	 * Get the original scaffold path directory
	 *
	 * @return string
	 */
	public function getScaffoldPath()
	{
		return base_path($this->cleanPath('vendor/rzian/scaffold/default')).DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the user scaffold path directory
	 *
	 * @return string
	 */
	public function getUserScaffoldPath()
	{
		return base_path($this->cleanPath('resources/scaffold')).DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the model path directory
	 *
	 * @return string
	 */
	public function getModelPath()
	{
		return base_path('app').DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the controller path directory
	 *
	 * @return string
	 */
	public function getControllerPath()
	{
		return base_path($this->cleanPath('app/Http/Controllers')).DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the view path directory
	 *
	 * @return string
	 */
	public function getViewPath()
	{
		return base_path($this->cleanPath('resources/views')).DIRECTORY_SEPARATOR;
	}

	/**
	 * Cleans the name of the path
	 *
	 * @return string
	 */
	public function cleanPath($name)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $name);
	}

	/**
     * Get the value of the protected attribute of a object
     *
     * @param string $name
     * @param object $object
     * @return mixed
     */
    public function forceGetAttribute($name, $object)
    {
    	if (! property_exists($object, $name))
    	{
    		return null;
    	}

        $property = new Property(get_class($object), $name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}