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
class Abstract_Collection extends Eden_Sql_Collection {
	/* Constants
	-------------------------------*/
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	/* Magic
	-------------------------------*/
	public function __construct() {
		$this->_database = Eden::i()->getActiveApp()->getDatabase();
	}
	
	/* Public Methods
	-------------------------------*/
	/**
	 * Adds a row to the collection
	 *
	 * @param array|Eden_Model
	 * @return this
	 */
	public function add($row = array()) {
		//Argument 1 must be an array or Eden_Model
		Abstract_Error::i()->argument(1, 'array', $this->_model);
		
		//if it's an array
		if(is_array($row)) {
			//make it a model
			$model = $this->_model;
			$row = $this->$model()->load($row);
		}
		
		//add it now
		$this->_list[] = $row;
		
		return $this;
	}
	
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}