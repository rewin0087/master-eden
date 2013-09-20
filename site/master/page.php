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
abstract class Master_Page extends Eden_Class {
	/* Constants
	-------------------------------*/
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_request 	 = NULL;
	protected $_message   	 = array();
	protected $_meta 		= array();
	protected $_head 		= array();
	protected $_body 		= array();
	protected $_foot 		= array();
	protected $_title 	   = 'Eden';
	protected $_class 	   = 'eden';
	protected $_template 	= NULL;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	/* Magic
	-------------------------------*/
	public function __construct(Eden_Registry $request = NULL) {
		$this->_request = $request;
	}
	
	public function __toString() {
		try {
			$output = $this->render();
		} catch(Exception $e) {
			Eden_Error_Event:: i()->exceptionHandler($e);
			
			return '';
		}
		
		if(is_null($output)) {
			return '';
		}
		
		return $output;
	}
	
	/* Public Methods
	-------------------------------*/
	/**
	 * Returns a string rendered version of the output
	 *
	 * @return string
	 */
	abstract public function render();
	
	/* Protected Methods
	-------------------------------*/
	
	protected function _render() {
		
		//set template path
		$tpl = $this->_request['path']['template'];
		
		//set data into the template template
		$head = app()->template($tpl.'/_head.php', $this->_head);
		$body = app()->template($tpl.$this->_template, $this->_body);
		$foot = app()->template($tpl.'/_foot.php', $this->_foot);
		
		//content of the page
		return app()->template($tpl.'/_page.php', array(
			'meta' 	=> $this->_meta,
			'title'   => $this->_title,
			'class'   => $this->_class,
			'head'	=> $head,
			'body'	=> $body,
			'foot'	=> $foot));
	}
	
	/*
	* Title: Set Message Content into master page template
	* @param: varchar
	* @param: text
	* @return: object
	*/
	protected function _setMessage($index, $content) {
		$this->_message[$index] = $content;
		
		return $this;	
	}
	
	/*
	* Title: Set Meta Content into master page template
	* @param: varchar
	* @param: text
	* @return: object
	*/
	protected function _setMeta($index, $content) {
		$this->_meta[$index] = $content;
		
		return $this;	
	}
	
	/*
	* Title: Set Template into master page template
	* @param: varchar
	* @return: object
	*/
	protected function _setTemplate($template) {
		$this->_template = $template;
		
		return $this;	
	}
	
	/*
	* Title: Set Body Content into master page template
	* @param: varchar
	* @param: text
	* @return: object
	*/
	protected function _setBody($index, $content) {
		$this->_body[$index] = $content;
		
		return $this;	
	}
	
	/*
	* Title: Set head Content into master page template 
	* @param: varchar
	* @param: text
	* @return: object
	*/
	protected function _setHead($index, $content) {
		$this->_head[$index] = $content;
		
		return $this;	
	}
	
	/*
	* Title: Set Foot Content into master page template
	* @param: varchar
	* @param: text
	* @return: object
	*/
	protected function _setFoot($index, $content) {
		$this->_foot[$index] = $content;
		
		return $this;	
	}
	
	/*
	* Title: Set Class into master page template
	* @param: varchar
	* @return: object
	*/
	protected function _setClass($class) {
		$this->_class = $class;
		
		return $this;	
	}
	
	/*
	* Title: Append Class from existing class
	* @param: varchar
	* @return: object
	*/
	protected function _appendClass($class) {
		$this->_class .= $class;
		
		return $this;	
	}
	
	/*
	* Title: Set Title into master page template
	* @param: varchar
	* @return: object
	*/
	protected function _setTitle($title) {
		$this->_title = $title;
		
		return $this;	
	}
	
	/*
	* Title: Append Title from existing title
	* @param: varchar
	* @return: object
	*/
	protected function _appendTitle($title) {
		$this->_title .= $title;
		
		return $this;	
	}
	
	/* Private Methods
	-------------------------------*/
}