<?php //-->
/*
 * This file is part a custom application package.
 */

/**
 * Default logic to output a page
 */
class App_Mysql extends Eden_Mysql {
  /* Constants
  -------------------------------*/
  /* Public Properties
  -------------------------------*/
  /* Protected Properties
  -------------------------------*/
 	protected $_cache      = NULL;
	protected $_results	= array();
	protected $_cacheCount = array(0, 0);
	protected $_useCache   = false;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function i() {
		return self::_getMultiple(__CLASS__);
	}
	
	/* Magic
	-------------------------------*/
	public function __construct($host, $name, $user, $pass = NULL, $port = NULL) {
		parent::__construct($host, $name, $user, $pass, $port);
		$root = App()->getRegistry()->get('path', 'root');
		$this->_cache = realpath($root.'/../cache/db');
	}
	
	/* Public Methods
	-------------------------------*/
	/**
	 * Queries the database
	 * 
	 * @param string query
	 * @param array binded value
	 * @return array|object
	 */
	public function query($query, array $binds = array()) {
		if(strpos(strtolower($query), 'insert into') === 0 
		|| strpos(strtolower($query), 'update') === 0
		|| strpos(strtolower($query), 'delete from') === 0) {
			//get the table
			$table = str_replace('insert into ', '', strtolower($query));
			$table = str_replace('update ', '', $table);
			$table = str_replace('delete from ', '', $table);
			list($table, $trash) = explode(' ', $table, 2);
			
			//invalidate all selects
			foreach($this->_results as $key => $result) {
				$lower = strtolower($key);
				if(strpos($lower, 'from '.$table) !== false 
				|| strpos($lower, 'join '.$table) !== false
				|| preg_match("/from\s[a-zA-z]+\.".$table."\s/i", $lower)
				|| preg_match("/join\s[a-zA-z]+\.".$table."\s/i", $lower)) {
					unset($this->_results[$key]);
				}
			}
			
			$keys = $this->_getCacheKeys();
			foreach($keys as $key) {
				$lower = strtolower($key);
				if(preg_match("/\-*".$table."\-*/i", $lower)) {
					$this->_removeCache($key);
				}
			}
		}
		
		if(strpos(strtolower($query), 'select') !== 0) {
			return parent::query($query, $binds);
		}
		
		$key = $this->_getCacheKey($query, $binds);
		
		if(isset($this->_results[$key])) {
			$this->_cacheCount[0]++;
			$this->_binds = array();
			return $this->_results[$key];
		} else if($this->_cacheKeyExists($key)) {
			$this->_cacheCount[0]++;
			$this->_binds = array();
			return $this->_getCache($key);
		}
		
		$results = parent::query($query, $binds);
		if(!$this->_useCache 
		|| strpos($query, date('Y-m-d H:i:s')) !== false
		|| strpos(json_encode($binds), date('Y-m-d H:i:s')) !== false) {
			return $results;
		}
		
		$this->_cacheCount[1]++;
		$this->_results[$key] = $results;
		$this->_setCache($key, $results);
		return $results;
	}
	
	public function getCacheCount() {
		return $this->_cacheCount;
	}
	
	public function useCache($yes) {
		$this->_useCache = $yes;
		return $this;
	}
	
	/* Protected Methods
	-------------------------------*/
	protected function _getCacheKeys() {
		$files = Eden_Folder::i($this->_cache)->getFiles();
		
		foreach($files as $i => $file) {
			$files[$i] = $file->pop();
		}
		
		return $files;
	}
	
	protected function _getCache($key) {
		return Eden_File::i($this->_cache.'/'.$key)->getData();
	}
	
	protected function _setCache($key, $value) {
		Eden_File::i($this->_cache.'/'.$key)->setData($value);
		
		return $this;
	}
	
	protected function _removeCache($key) {
		if(!file_exists($this->_cache.'/'.$key)) {
			return $this;
		}
		
		Eden_File::i($this->_cache.'/'.$key)->remove();
		return $this;
	}
	
	protected function _cacheKeyExists($key) {
		return file_exists($this->_cache.'/'.$key);
	}
	
	protected function _getCacheKey($query, $binds) {
		$raw = $query.json_encode($binds);
		
		$tables = explode(' FROM ', str_replace(array('from', 'JOIN', 'join'), 'FROM', $query));
		
		foreach($tables as $i => $table) {
			if($i == 0) {
				unset($tables[$i]);
				continue;
			}
			
			$table = substr(trim($table), 0, strpos($table, ' '));
			
			if(!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
				unset($tables[$i]);
				continue;
			}
			
			$tables[$i] = $table;
		}
		
		$tables = array_unique($tables);
		
		return implode('-', $tables).'-'.md5($raw);
	}
  /* Private Methods
  -------------------------------*/
}