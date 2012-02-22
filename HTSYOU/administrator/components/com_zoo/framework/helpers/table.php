<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TableHelper
		Helper for database tables
*/
class TableHelper extends AppHelper {

	/* table prefix */
	protected $_prefix;

	/* tables */
	protected $_tables = array();
    
	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// set table prefix
		$this->_prefix = '#__'.$this->app->id.'_';

		// load class
		$this->app->loader->register('AppTable', 'classes:table.php');
	}

	/*
		Function: get
			Retrieve a table

		Parameters:
			$name - Table name
			$prefix - Table prefix

		Returns:
			Mixed
	*/
	public function get($name, $prefix = null) {
		
		// load table class
		$class = $name.'Table';
		$this->app->loader->register($class, 'tables:'.strtolower($name).'.php');

		// set tables prefix
		if ($prefix == null) {
			$prefix = $this->_prefix;
		}
		
		// add table, if not exists
		if (!isset($this->_tables[$name])) {
			$this->_tables[$name] = class_exists($class) ? new $class($this->app) : new AppTable($this->app, $prefix.$name);
		}

		return $this->_tables[$name];
	}
	
	/*
		Function: __get
			Retrieve a table

		Parameters:
			$name - Table name

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->get($name);
	}
	
}