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
class Test extends Abstract_Directory {
	/* Constants
	-------------------------------*/
	const COLLECTION	= 'Test_Collection';
	const SEARCH		= 'Test_Search';
	const MODEL		 = 'Test_Model';
	
	const TEST_TABLE 	= 'test';
	const TEST_ID	   = 'test_id';
	
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_search	   = self::SEARCH;
	protected $_collection   = self::COLLECTION;
	protected $_model		= self::MODEL;
	protected $_table		= self::TEST_TABLE;
	protected $_primary	  = self::TEST_ID;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function i() {
		return self::_getMultiple(__CLASS__);
	}
	
	/* Magic
	-------------------------------*/
	/* Public Methods
	-------------------------------*/
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}
