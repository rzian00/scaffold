<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/8/18 02:55 PM
 */

namespace Rzian\Scaffold\Console\Commands;

use Illuminate\Support\Facades\DB;
use Rzian\Scaffold\Table;

class ModelGenerator extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:model
                            {table : Name of the schema table from the database.}
                            {name : Name of the model class path. (Can include namespace.)}
                            {--scaffold=default : The scaffold folder name in the resources}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a model class based from the schema table';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (class_exists($class = 'App\\'.($name = $this->argument('name')))
            && ! $this->confirm(sprintf(static::INFO_OVERWRITE, 'Model', $class), true))
        {
            exit($this->info(static::INFO_ABORTED));                
        }

        $table = $this->getTable($this->argument('table'));
        $primaryKey = $table->getPrimaryKey();
        $fillables = '';
        $rules = '';
        foreach($table->getColumns() as $key => $column)
        {
            if ($key === $primaryKey) continue;
            $key = sprintf(",\n\t\t'%s'", $key);
            if ($column->getNotNull()) $rules .= sprintf("%s => 'required'", $key);
            $fillables .= $key;
        }

        $namespace = explode('\\', $class);
        $model = array_pop($namespace);

        $this->generate('model', sprintf('%s.php', $this->getModelPath().str_replace('\\', DIRECTORY_SEPARATOR, $name)), [
            'namespace' => implode('\\', $namespace),
            'name' => $model,
            'table' => $table->getName(),
            'fillables' => substr($fillables, 1),
            'rules' => substr($rules, 1),
            'relations' => $this->getRelations($table)
        ]);
                    
        return $this->info(sprintf(static::INFO_SUCCESS, 'Model', $model));
    }

    /**
     * Get the relation content
     *
     * @param \Rzian\Scaffold\Table $table 
     * @return mixed
     */
    public function getRelations(Table $table)
    {
        if (! $references = $table->getForeignReferences())
        {
            return '';
        }
        
        $relations = array();
        foreach($references as $key => $_table)
        {
            $relations[] = preg_replace([
                    $this->delimiter('class'),
                    $this->delimiter('key'),
                    $this->delimiter('name'),
                    $this->delimiter('method')
                ], [
                    'App\\'.ucfirst($_table->getPreferredModelName()),
                    $_table->getPrimaryKey(),
                    $key,
                    rtrim($key, '_id')
                ],
                $this->getContent('model.relation')
            );
        }

        return implode("\n\n", $relations);
    }
}