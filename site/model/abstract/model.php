<?php //-->
/*
 * This file is part a custom application package.
 */

/**
 * The base class for any class that defines a view.
 * A view controls how templates are loaded as well as 
 * being the final point where data manipulation can occur.
 *
 * @package    Eden
 */
class Abstract_Model extends Eden_Sql_Model {
	/* Constants
	-------------------------------*/
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_primary = NULL;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function i() {
		return self::_getMultiple(__CLASS__);
	}
	
	/* Magic
	-------------------------------*/
	public function __construct() {
		$this->_database = Eden::i()->getActiveApp()->getDatabase();
	}
	
	/* Public Methods
	-------------------------------*/
	/**
	 * Return model
	 *
	 * @param scalar|null
	 * @param string
	 * @return Eden_Mysql_Model
	 */
	public function load($value, $column = NULL) {
		Abstract_Error::i()
			->argument(1, 'scalar', 'array')
			->argument(2, 'string', 'null');
		
		if(is_array($value)) {
			return $this->set($value);
		}
		
		if(is_null($column)) {
			$column = $this->_primary;
		}
		
		$row = $this->_database->getRow($this->_table, $column, $value);
		
		if(is_null($row)) {
			return $this;
		}
		
		return $this->set($row);
	}
	
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}

