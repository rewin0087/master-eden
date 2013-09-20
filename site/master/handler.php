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
class Master_Handler extends Eden_Class {
	/* Constants
	-------------------------------*/
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_registry = NULL;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function i() {
		return self::_getSingleton();
	}
	
	/* Magic
	-------------------------------*/
	public function __construct(Eden $app) {
		
		$this->_registry = $app->getRegistry();
		
		$app->listen('request', $this, 'sessionStart');
		
		//$app->listen('request', $this, 'clearQuote');
		
		if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
			
			$app->listen('request', $this, 'logoutUser');
		}
		
	}
	
	/* Public Methods
	-------------------------------*/
	/* Start session
	 *
	 * return this
	 */
	public function sessionStart() {
		$session = $this->Eden_Session();		
		
		$this->_registry->set('session', 'data', $session);
		
		$this->_registry->set('session', 'id', $session->getId());
		
		return $this;
	}
	
	/* user logout
	 *
	 * return this
	 */
	public function logoutUser() {
		//unset sessions
		unset($_SESSION['user']);
		header('Location: /');
		exit;
	}
	
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}