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
class Abstract_Directory extends Eden_Class {
	/* Constants
	-------------------------------*/	
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_app 		= NULL;
	protected $_search		= NULL;
	protected $_collection	= NULL;
	protected $_model		= NULL;
	protected $_table		= NULL;
	protected $_primary		= NULL;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/	
	/* Magic
	-------------------------------*/
	public function __construct() {		
		$this->_app = Eden::i()->getActiveApp();
	}
	
	/* Public Methods
	-------------------------------*/
	/**
	 * Return search
	 *
	 * @param string
	 * @return Eden_Mysql_Search
	 */
	public function search() {
		$search = $this->_search;
		return $this->$search();
	}
	
	/**
	 * Return model
	 *
	 * @param scalar|null
	 * @param string
	 * @return Eden_Mysql_Model
	 */
	public function model($value = NULL, $column = NULL) {
		Abstract_Error::i()
			->argument(1, 'scalar', 'array', 'null')
			->argument(2, 'string', 'null');
		
		$model = $this->_model;
		$model = $this->$model();
		
		if(is_null($value)) {
			return $model;
		}
		
		if(is_array($value)) {
			return $model->set($value);
		}
		
		if(is_null($column)) {
			$column = $this->_primary;
		}
		
		$row = $this->_app->getDatabase()->getRow($this->_table, $column, $value);
		
		if(is_null($row)) {
			return $model;
		}
		
		return $model->set($row);
	}
	
	/**
	 * Return collection
	 *
	 * @param array
	 * @return Eden_Mysql_Collection
	 */
	public function collection(array $list = array()) {		
		$collection = $this->_collection;
		return $this->$collection()->set($list);
	}
	
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}