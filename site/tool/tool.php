<?php //-->
/*
 * This file is part a custom application package.
 */

/**
 * @package    Eden
 */
class Tool extends Eden_Class {
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
	public static function i(Eden_Registry $request = NULL) {
		return self::_getMultiple(__CLASS__, $request);
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
