<?php //-->

/**
 * Pagination block
 *
 * @package    Eden
 * @category   site
 * @author     Christian Blanquera cblanquera@openovate.com
 */
class Master_Block_Badge extends Eden_Block {
	/* Constants
	-------------------------------*/
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_data = array();
	
	/* Private Properties
	-------------------------------*/
	/* Magic
	-------------------------------*/
	public static function i() {
		return self::_getMultiple(__CLASS__);
	}
	
	public function __construct($data) {
		$this->_data = $data;
	}
	
	/* Public Methods
	-------------------------------*/
	
	/**
	 * Returns the template variables in key value format
	 *
	 * @param array data
	 * @return array
	 */
	public function getVariables() {
		
		return array('data' => $this->_data);
	}
	
	/**
	 * Returns a template file
	 * 
	 * @param array data
	 * @return string
	 */
	 public function getTemplate() {
		return realpath(dirname(__FILE__).'/template/test.php');
	}	
}