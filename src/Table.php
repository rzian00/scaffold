<?php
/**
 * Created by PhpStorm.
 * User: Ian Moreno <rzian00@yahoo.com>
 * Date: 1/10/18 03:33 PM
 */

namespace Rzian\Scaffold;

use Illuminate\Support\Facades\DB;

class Table
{
	/**
	 * @var string
	 */
	protected $_name;

	/**
	 * The constructor
	 *
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->_name = $name;		
	}

	/**
	 * Get a schema builder instance for the connection
	 *
	 * @return \Illuminate\Database\Schema\Builder
	 */
	protected static function getBuilder()
	{
		return DB::getSchemaBuilder();
	}

	/**
	 * Get the list of column name
	 *
	 * @return array
	 */
	public function getColumnNames()
	{
		return $this->getBuilder()->getColumnListing($this->getName());
	}

	/**
	 * Get the list of column definition
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->getManager()->listTableColumns($this->getName(true));
	}	

	/**
	 * Get the list of foreign key
	 *
	 * @return array
	 */
	public function getForeignKeys()
	{
		return $this->getManager()->listTableForeignKeys($this->getName(true));
	}

	/**
	 * Get the list of foreign relation mapping
	 *
	 * @return array|\Rzian\Scaffold\Table[]
	 */
	public function getForeignReferences()
	{				
		$references = array();
        foreach($this->getForeignKeys() as $foreigner)
        {
        	$key = current($foreigner->getLocalColumns());        	
            $references[$key] = new static(self::removePrefix($foreigner->getForeignTableName()));                    
        }

        return $references;
	}

	/**
	 * Get the list of index key
	 *
	 * @return array
	 */
	public function getIndexes()
	{
		return $this->getManager()->listTableIndexes($this->getName(true));
	}

	/**
	 * Get the Doctrine DBAL schema manager for the connection
	 *
	 * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	protected function getManager()
	{
		return DB::getDoctrineSchemaManager();
	}
	
	/**
	 * Get the name of the schema table
	 *
	 * @param string $name
	 * @return string
	 */
	public function getName($isWithPrefix = false)
	{
		return $isWithPrefix ? DB::getTablePrefix() . $this->_name : $this->_name;
	}

	/**
	 * Get the primary key of the schema table
	 *
	 * @param string $name
	 * @return string
	 */
	public function getPrimaryKey()
	{
		$indexes = $this->getIndexes();

		if (! isset($indexes['primary']))
		{
			return null;
		}

		return current($indexes['primary']->getColumns());
	}

	/**
	 * Get the instance of the primary key
	 *
	 * @param string $name
	 * @return string
	 */
	public function getPrimaryKeyInstance()
	{
		$indexes = $this->getIndexes();

		return isset($indexes['primary']) ? $indexes['primary'] : null;
	}

	/**
	 * Get the preferred model name of the schema table
	 *	 
	 * @return string
	 */
	public function getPreferredModelName()
	{
		return str_singular($this->getName());
	}

	/**
	 * Removes the prefix name of the given table name
	 *
	 * @param string $name
	 * @return string
	 */
	public static function removePrefix($name)
	{
		return ltrim($name, DB::getTablePrefix());
	}

	/**
	 * Check the existence of the schema table
	 *
	 * @param string $name
	 * @return string
	 */
	public static function isExists($name)	
	{
		return self::getBuilder()->hasTable($name);
	}	
}