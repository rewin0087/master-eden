
<?php //-->
/*
 * This file is part of the Eden package.
 * (c) 2009-2011 Christian Blanquera <cblanquera@gmail.com>
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
require_once dirname(__FILE__).'/../library/eden.php'; 

function app() {
	$class = App::i();
	
	if(func_num_args() == 0) {
		return $class;
	}
	
	$args = func_get_args();
	return $class->__invoke($args);
}

/**
 * Defines the starting point of every site call.
 * Starts laying out how classes and methods are handled.
 *
 * @package    Eden
 * @category   site
 * @author     Christian Blanquera <cblanquera@gmail.com>
 * @version    $Id: application.php 9 2010-01-12 15:42:40Z blanquera $
 */
class App extends Eden {
	/* Constants
	-------------------------------*/
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected $_database 	 = NULL;
	protected $_cache		= NULL;
	protected $_registry	 = NULL;
	protected $_pages 		= array();
	protected static $_uid   = 0;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function i() {
		return self::_getSingleton(__CLASS__);
	}
	
	/* Magic
	-------------------------------*/
	public function __construct() {
		if(!self::$_active) {
			self::$_active = $this;
		}
		
		$this->_root = dirname(__FILE__);
		
		$this->setLoader();
		
		//require registry
		$this->_registry = Eden_Loader::i()
			->addRoot(dirname(__FILE__).'/../library')
			->load('Eden_Registry')
			->Eden_Registry();
	}
	
	/* Public R/R Methods
	-------------------------------*/
	/**
	 * Sets class routes
	 *
	 * @param string|array routes
	 * @return Eden_Framework
	 */
	public function routeClasses($routes) {
		Eden_Error::i()->argument(1, 'string', 'array', 'bool');
		$route = Eden_Route::i()->getClass();
		
		if($routes === true) {
			$route->route('Cache', 		'Eden_Cache')
				->route('Registry', 	'Eden_Registry')
				->route('Model', 		'Eden_Model')
				->route('Cookie', 		'Eden_Cookie')
				->route('Session', 		'Eden_Session')
				->route('Template', 	'Eden_Template')
				->route('Curl', 		'Eden_Curl')
				->route('Event', 		'Eden_Event')
				->route('Path', 		'Eden_Path')
				->route('File', 		'Eden_File')
				->route('Folder', 		'Eden_Folder')
				->route('Image', 		'Eden_Image')
				->route('Mysql', 		'Eden_Mysql')
				->route('Type', 		'Eden_Type')
				->route('Validation', 	'Eden_Validation');
			
			return $this;
		}
		
		if(is_string($routes)) {
			$routes = include($routes);
		}
		
		foreach($routes as $alias => $class) {
			$route->route($alias, $class);
		}
		
		return $this;
	}
	/**
	 * Lets the framework handle exceptions.
	 * This is useful in the case that you 
	 * use this framework on a server with
	 * no xdebug installed.
	 *
	 * @param string|null the error type to report
	 * @param bool|null true will turn debug on
	 * @return this
	 */
	public function setDebug($reporting = NULL, $default = NULL) {
		Eden_Error::i()->noArgTest()->argument(1, 'int', 'null')->argument(2, 'bool', 'null');
		
		Eden_Loader::i()
			->load('Eden_Template')
			->Eden_Error_Event()
			->when(!is_null($reporting), 1)
			->setReporting($reporting)
			->when($default === true, 4)
			->setErrorHandler()
			->setExceptionHandler()
			->listen('error', $this, 'error')
			->listen('exception', $this, 'error')
			->when($default === false, 4)
			->releaseErrorHandler()
			->releaseExceptionHandler()
			->unlisten('error', $this, 'error')
			->unlisten('exception', $this, 'error');
		
		return $this;
	}
	
	/**
	 * Sets the application absolute paths
	 * for later referencing
	 * 
	 * @return this
	 */
	public function setPaths(array $paths = array()) {
		$default = array(
			'root' 			 => $this->_root,
			'model'			=> $this->_root.'/model',
			'web'			  => $this->_root.'/web', 
			'assets'		   => $this->_root.'/web/assets',
			'cache'			=> realpath($this->_root.'/../cache'),
			'config'		   => $this->_root.'/app/config',
			'template'		 => $this->_root.'/app/template',
			'upload'		   => realpath($this->_root.'/web/image'));
		
		//foreach default path
		foreach($default as $key => $path) {
			$this->_registry->set('path', $key, $path);
		}
		
		//for each path
		foreach($paths as $key => $path) {
			//make them absolute
			$path = (string) Eden_Path::i($path)->absolute();
			//set it
			$this->_registry->set('path', $key, $path);
		}
		
		return $this;
	}
	
	/**
	 * Sets page aliases
	 *
	 * @return this
	 */
	public function setPages($pages, $absolute = false) {
		App_Error::i()
			->argument(1, 'string', 'array')
			->argument(2, 'bool');
			
		if(is_string($pages)) {
			if(!$absolute) {
				$pages = $this->_registry->get('path', 'config').'/'.$pages;
			}
			
			$pages = include($pages);
		}
		
		$this->_pages = $pages;
		return $this;
	}
	
	/**
	 * Sets request
	 *
	 * @param EdenRegistry|null the request object
	 * @return this
	 */
	public function setRequest(Eden_Registry $request = NULL) {
		//if no request
		if(is_null($request)) {
			//set the request
			$request = $this->_registry;
		}
		
		$path = '/';
		if(isset($_SERVER['REQUEST_URI'])) {
			$path = $_SERVER['REQUEST_URI'];
			unset($_SERVER['REQUEST_URI']);
		}
		
		//determine a request path
		$path = str_replace('favicon.ico', '/', $path);
		
		//fix the request path
		$path = (string) Eden_Path::i($path);
		
		//get the path array
		$pathArray = explode('/',  $path);
			
		//determine the page suggestions
		$suggestions = array();
		//for each of the page pattern to paths			
		foreach($this->_pages as $pagePattern => $pagePath) {
			//we need to replace % with .*
			//so for example /page/%/edit is the same as /page/.*/edit
			$pattern = '%'.str_replace('%', '.*', $pagePattern).'%';
			//if the path matches the response path key
			if(preg_match($pattern, $path)) {
				//add the path and the length to the suggestions
				//we do this because we need to sort the suggestions
				//by relavancy
				$suggestions[strlen($pagePattern)][] = array($pagePattern, $pagePath);
			}
		}
		
		//by default the page is the home page
		$page = NULL;
		//when given a path like /page/1/edit
		//and a pattern like /page/%/edit
		//we can determine one of the path variables is 1
		//make a place holder for path variables
		//which we will find later
		$variables 	= array();
		//to do that we need suggestions
		//so if we have suggestions
		if(!empty($suggestions)) {
			//sort suggestions by length because the more detailed the
			//page link the more probable this is the page they actually want
			krsort($suggestions);
			
			//for the page to fetch we only care about the first suggestion and 
			//second index in array($pagePattern, $pagePath)
			$suggestions = array_shift($suggestions);
			$suggestions = array_shift($suggestions);
			$page = $suggestions[1];				
			
			//lets determine the path variables
			$variables = $this->_getPageVariables($path, $suggestions[0]);
		} else { 
			$classBuffer = $pathArray;
			while(count($classBuffer) > 1) {
				$classParts = ucwords(implode(' ', $classBuffer)); 
				$page = 'App_Page'.str_replace(' ', '_', $classParts);
				if(class_exists($page)) {
					break;
				}
				
				$variable = array_pop($classBuffer);
				array_unshift($variables, $variable);
			}
			
			if(count($classBuffer) == 1) {
				$page = NULL;
			}
		}
		
		$path = array(
			'string' 	=> $path, 
			'array' 	=> $pathArray,
			'variables'	=> $variables);
		
		//set the request
		$request->set('server', $_SERVER)
			->set('cookie', $_COOKIE)
			->set('get', $_GET)
			->set('post', $_POST)
			->set('files', $_FILES)
			->set('request', $path)
			->set('page', $page);
		
		return $this;
	}
	
	/**
	 * Sets response
	 *
	 * @param EdenRegistry|null the request object
	 * @return this
	 */
	public function setResponse($default, Eden_Registry $request = NULL) {
		//if no request
		if(is_null($request)) {
			//set the request
			$request = $this->_registry;
		}
		
		$page = $request->get('page');
		
		if(!$page || !class_exists($page)) {
			$page = $default;
		}
		
		//set the response data
		$response = $this->$page($request);
		$request->set('response', $response);
		
		return $this;
	}
	
	/**
	 * returns the response
	 *
	 * @param EdenRegistry|null the request object
	 * @return string
	 */
	public function getResponse(Eden_Registry $request = NULL) {
		//if no request
		if(is_null($request)) {
			//set the request
			$request = $this->_registry;
		}
		
		return $request->get('response');
	}
	
	/* Public Database Methods
	-------------------------------*/
	/**
	 * Sets up the default database connection
	 *
	 * @return this
	 */
	public function addDatabase($key, 			$type = NULL, 
								$host = NULL, 	$name = NULL, 
								$user = NULL, 	$pass = NULL, 
								$default = true) {
		App_Error::i()
			->argument(1, 'string', 'array', 'null')
			->argument(2, 'string', 'null')
			->argument(3, 'string', 'null')
			->argument(4, 'string', 'null')
			->argument(5, 'string', 'null')
			->argument(6, 'string', 'null')
			->argument(7, 'bool');
			
		if(is_array($key)) {
			$type 		= isset($key['type']) ? $key['type'] : NULL;
			$host 		= isset($key['host']) ? $key['host'] : NULL;
			$name 		= isset($key['name']) ? $key['name'] : NULL;
			$user 		= isset($key['user']) ? $key['user'] : NULL;
			$pass 		= isset($key['pass']) ? $key['pass'] : NULL;
			$default 	= isset($key['default']) ? $key['default'] : true;
			$key 		= isset($key['key']) ? $key['key'] : NULL;
		}
		
		//connect to the data as described in the config
		switch($type) {
			case 'postgre':
				$database = Eden_Pgsql::i($host, $name, $user, $pass);
				break;
			case 'mysql':
				$database = App_Mysql::i($host, $name, $user, $pass); 
				break;
		}
		
		$this->_registry->set('database', $key, $database);
		
		if($default) {
			$this->_database = $database;
		}
		
		return $this;
	}
	
	/**
	 * Returns the default database instance
	 *
	 * @return Eden_Database_Abstract
	 */
	public function getDatabase($key = NULL) {
		if(is_null($key)) {
			//return the default database
			return $this->_database;
		}
		
		return $this->_registry->get('database', $key);
	}
	
	/**
	 * Sets the default database
	 *
	 * @param string key
	 * @return this
	 */
	public function setDefaultDatabase($key) {
		App_Error::i()->argument(1, 'string');
		
		$args = func_get_args();
		//if the args are greater than 5
		//they mean to add it
		if(count($args) > 5) {
			$this->addDatabase($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
		}
		
		//now set it
		$this->_database = $this->_registry->getValue('database', $key);
		
		return $this;
	}
	
	/* Public Event Methods
	-------------------------------*/
	/**
	 * Starts filters. Filters will handle when to run.
	 *
	 * @param string|array handlers
	 * @return Eden_Application
	 */
	public function setFilters($filters) {
		App_Error::i()->argument(1, 'string', 'array');
		
		if(is_string($filters)) {
			$filters = include($filters);
		}
		
		//for each handler as class
		foreach($filters as $class) {
			//try to
			try {
				//instantiate the class
				$this->$class($this);
			//when there's an error do nothing
			} catch(Exception $e){}
		}
		
		return $this;
	}
	
	/* Public Misc Methods
	-------------------------------*/
	/**
	 * Sets the cache
	 * 
	 * @return this
	 */
	public function setCache($root) {
		//we need Eden_Path to fix the path formatting
		if(!class_exists('Eden_Path')) {
			Eden_Loader::i()->load('Eden_Path');
		}
		
		//format the path
		$root = (string) Eden_Path::i($root);
		
		// Start the Global Cache
		Eden_Cache::i($root);
		
		return $this;
	}
	
	/**
	 * Returns the current Registry
	 *
	 * @return Eden_Registry
	 */
	public function getRegistry() {
		return $this->_registry;
	}
	
	/**
	 * Returns the template loaded with specified data
	 *
	 * @param array
	 * @return Eden_Template
	 */
	public function template($file, array $data = array()) {
		App_Error::i()->argument(1, 'string');
		return Eden_Template::i()->set($data)->parsePhp($file);
	}
	
	/**
	 * Returns the template loaded with specified data
	 *
	 * @param array
	 * @return Eden_Template
	 */
	public function block($name) {
		App_Error:: i()->argument(1, 'string');
		$args 	= func_get_args();
		$name 	= array_shift($args);
		
		$class 	= 'App_Block_'.ucwords($name);
		if(class_exists($class)) {
			return Eden_Route:: i()->getClass($class, $args);
		}
		
		$class 	= 'Eden_Block_'.ucwords($name);
		if(class_exists($class)) {
			return Eden_Route:: i()->getClass($class, $args);
		}
		
		return false;
	}
	
	public function output($variable) {
		print '<pre>';
		print_r($variable);
		print '</pre>';
	}
	
	public function helper($file) {
		return require_once dirname(__FILE__).'/helper/'.$file;
	}
	
	/**
	 * Returns a mysql model
	 *
	 * @param string
	 * @return Eden_Mysql_Model
	 */
	public function model($table, $data = NULL, $column = NULL) {
		//argument test
		Product_Error:: i()
			->argument(1, 'string')								
			->argument(2, 'scalar', 'array', 'null')	
			->argument(3, 'string', 'null');					
		
		if(is_null($data)) {
			$data = array();
		}
		
		if(is_array($data)) {
			return $this->getDatabase()->model($data)->setTable($table);
		}
		
		$database = $this->getDatabase();
		
		if(is_null($column)) {
			$column = $database()->getPrimaryKey($table);			
		}
		
		if(is_null($column)) {
			return $database()->model()->setTable($table);
		}
		
		return $this->getDatabase()->getModel($table, $column, $data);
			
		//if data is not null
		if(!is_null($data)) {
			$model->load($data, $column);
		}
	}
	
	public function getQuery(array $query, $key, $value) {
		if(isset($query[$key]) && is_array($query[$key]) && is_array($value)) {
			$query[$key] = array_merge($query[$key], $value);
		} else {
			$query[$key] = $value;
		}
		
		return http_build_query($query);
	}
	
	public function getSortQuery(array $query, $sort, $default = 'ASC') {
		if(!isset($query['sort']) || $query['sort'] != $sort) {
			$query['sort'] = $sort;
			$query['order'] = $default;
			
			return http_build_query($query);
		}
		
		if($query['order'] == 'ASC') {
			$query['order'] = 'DESC';
		} else if(isset($query['order'], $query['sort'])) {
			$query['order'] = $default;
		}
		
		return http_build_query($query);
	}
	
	/**
	 * Error trigger output
	 *
	 * @return void
	 */
	public function error($e, $event, $type, $level, $class, $file, $line, $message, $trace, $offset) {
		$history = array();
		for(; isset($trace[$offset]); $offset++) {
			$row = $trace[$offset];
			 
			//lets formulate the method
			$method = $row['function'].'()';
			if(isset($row['class'])) {
				$method = $row['class'].'->'.$method;
			}
			 
			$rowLine = isset($row['line']) ? $row['line'] : 'N/A';
			$rowFile = isset($row['file']) ? $row['file'] : 'Virtual Call';
			 
			//add to history
			$history[] = array($method, $rowFile, $rowLine);
		}
		
		
		
		echo Eden_Template::i()
			->set('history', $history)
			->set('type', $type)
			->set('level', $level)
			->set('class', $class)
			->set('file', $file)
			->set('line', $line)
			->set('message', $message)
			->parsePhp(dirname(__FILE__).'/app/template/error.php');
	}
	
	public function uid() {
		return self::$_uid++;
	}
	
	/* Protected Methods
	-------------------------------*/
	protected function _getPageVariables($requestPath, $pagePath) {
		//if requestPath is not string
		if(!is_string($requestPath)) {
			//throw exception
			throw new Eden_Site_Exception(sprintf(Eden_Exception::NOT_STRING, 1));
		}
		
		//if pagePath is not string
		if(!is_string($pagePath)) {
			//throw exception
			throw new Eden_Site_Exception(sprintf(Eden_Exception::NOT_STRING, 2));
		}
		
		$variables 			= array();
		
		//fix paths
		$requestPath = (string) $this->Eden_Path($requestPath);
		
		//if the request path equals /
		if($requestPath == '/') {
			//there would be no page variables
			return array();
		}
		
		$pagePath = (string) $this->Eden_Path($pagePath);
		
		//get the arrays
		$requestPathArray 	= explode('/', $requestPath);
		$pagePathArray 		= explode('/', $pagePath);
		
		//we do not need the first path because
		// /page/1 is [null,page,1] in an array
		array_shift($requestPathArray);
		array_shift($pagePathArray);
		
		//for each request path
		foreach($requestPathArray as $i => $value) {
			//if the page path is not set, is null or is '%'
			if(!isset($pagePathArray[$i]) 
				|| trim($pagePathArray[$i]) == NULL 
				|| $pagePathArray[$i] == '%') {
				//then we can assume it's a variable
				$variables[] = $requestPathArray[$i];
			}
		}
		
		return $variables;
	}
	
	protected function _getArguments($arguments) {
		if(empty($arguments)) {
			return array();
		}
		
		//for each argument
		foreach($arguments as $i => $argument) {
			//is it a string ?
			if(is_string($argument)) {
				$argument = "'".$argument."'";
			//is it an array ?
			} else if(is_array($argument)) {
				$argument = 'Array';
			//is it an object ?
			} else if(is_object($argument)) {
				$argument = get_class($argument);
			//is it a bool ?
			} else if(is_bool($argument)) {
				$argument = $argument ? 'true' : 'false';
			//is it null ?
			} else if(is_null($argument)) {
				$argument = 'null';
			}
			
			$arguments[$i] = $argument;
		}
		
		return $arguments;
	}
	
	/* Private Methods
	-------------------------------*/
}
