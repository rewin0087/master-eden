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
class Abstract_Search extends Eden_Sql_Search {
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
	 * Returns the results in a collection
	 *
	 * @return Eden_Mysql_Collection
	 */
	public function getCollection() {
		$collection = $this->_collection;
		return $this->$collection()->set($this->getRows());
	}
	
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}