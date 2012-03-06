<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: RequestWidgetkitHelper
		Helper for managing/retrieving request variables.
*/
class RequestWidgetkitHelper extends WidgetkitHelper {

	/* wrapped class */
	protected $_class = 'JRequest';

	/*
		Function: get
			Retrieve a value from a request variable

		Parameters:
			$var - Variable name (hash:name)
			$type - Variable type (string, int, float, bool, array, word, cmd)
			$default - Default value

		Returns:
			Mixed
	*/	
    public function get($var, $type, $default = null) {
		
		// parse variable name
		extract($this->_parse($var));

		// get hash array, if name is empty
		if ($name == '') {
			return $this->_call(array($this->_class, 'get'), array($hash));
		}
		
		// access a array value ?
		if (strpos($name, '.') !== false) {

			$parts = explode('.', $name);
			$array = $this->_call(array($this->_class, 'getVar'), array(array_shift($parts), $default, $hash, 'array'));

			foreach ($parts as $part) {

				if (!is_array($array) || !isset($array[$part])) {
					return $default;
				}

				$array =& $array[$part];
			}

			return $array;
		}

		return $this->_call(array($this->_class, 'getVar'), array($name, $default, $hash, $type));
    }

	/*
		Function: set
			Set a request variable

		Parameters:
			$var - Variable name (hash:name)
			$value - Variable value

		Returns:
			RequestHelper
	*/	
    public function set($var, $value = null) {
		
		// parse variable name
		extract($this->_parse($var));

		if (!empty($name)) {
			
			// set default hash to method
			if ($hash == 'default') {
				$hash = 'method';
			}
			
			// set a array value ?
			if (strpos($name, '.') !== false) {

				$parts = explode('.', $name);
				$name  = array_shift($parts);
				$array =& $this->_call(array($this->_class, 'getVar'), array($name, array(), $hash, 'array'));
				$val   =& $array;

				foreach ($parts as $i => $part) {

					if (!isset($array[$part])) {
						$array[$part] = array();
					}

					if (isset($parts[$i + 1])) {
						$array =& $array[$part];
					} else {
						$array[$part] = $value;
					}
				}

				$value = $val;
			}
						
			$this->_call(array($this->_class, 'setVar'), array($name, $value, $hash));
		}

		return $this;
    }

	/*
		Function: __call
			Map all functions to JRequest class

		Parameters:
			$name - Method name
			$args - Method arguments

		Returns:
			Mixed
	*/	
    public function __call($method, $args) {
		return $this->_call(array($this->_class, $method), $args);
    }
	
	/*
		Function: _parse
			Parse variable string.

		Parameters:
			$var - Variable

		Returns:
			String
	*/
	protected function _parse($var) {
	    
	    // init vars
		$parts = explode(':', $var, 2);
		$count = count($parts);
		$name  = '';
		$hash  = 'default';

		// parse variable name
		if ($count == 1) {
			list($name) = $parts;
		} elseif ($count == 2) {
			list($hash, $name) = $parts;
		}
		
		return compact('hash', 'name');
    }
	
}