<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppHelper
		Helper base class
*/
class AppHelper {

	/* app instance */
	public $app;

	/* helper name */
	protected $_name;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($app) {

		// set application
		$this->app = $app;

		// set default name
		$this->_name = strtolower(basename(get_class($this), 'Helper'));

	}

	/*
		Function: getName
			Get helper name

		Returns:
			String
	*/	
	public function getName() {
		return $this->_name;
	}

	/*
		Function: _call
			Execute function call

		Returns:
			Mixed
	*/	
	protected function _call($function, $args = array()) {

		if (is_array($function)) {

			list($object, $method) = $function;

			if (is_object($object)) {
				switch (count($args)) { 
					case 0 :
						return $object->$method();
						break;
					case 1 : 
						return $object->$method($args[0]); 
						break; 
					case 2: 
						return $object->$method($args[0], $args[1]); 
						break; 
					case 3: 
						return $object->$method($args[0], $args[1], $args[2]); 
						break; 
					case 4: 
						return $object->$method($args[0], $args[1], $args[2], $args[3]); 
						break; 
				} 
			}

		}

		return call_user_func_array($function, $args);                               
	}

}