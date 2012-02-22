<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AppData
		Read/Write data in various formats.
*/
class AppData extends ArrayObject {

	/*
		Function: __construct
			Constructor
	*/
	public function __construct($data = array()) {
		parent::__construct($data ? $data : array());
	}

	/*
		Function: has
			Has a key ?

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function has($name) {
		return $this->offsetExists($name);
	}

	/*
		Function: get
			Get a value from array

		Parameters:
			$key - Array key
			$default - Default value, return if key was not found

		Returns:
			Mixed
	*/
	public function get($key, $default = null) {

		if ($this->offsetExists($key)) {
			return $this->offsetGet($key);
		}

		return $default;
	}



 	/*
		Function: set
			Set a value

		Parameters:
			$name - String
			$value - Mixed

		Returns:
			Void
	*/
	public function set($name, $value) {
		$this->offsetSet($name, $value);
		return $this;
	}

	/*
		Function: remove
			Remove a value

		Parameters:
			$name - String

		Returns:
			Void
	*/
	public function remove($name) {
		$this->offsetUnset($name);
		return $this;
	}

	/*
		Function: __isset
			Has a key ? (via magic method)

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function __isset($name) {
		return $this->offsetExists($name);
	}

	/*
		Function: __get
			Get a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->offsetGet($name);
	}

 	/*
		Function: __set
			Set a value (via magic method)

		Parameters:
			$name - String
			$value - Mixed

		Returns:
			Void
	*/
	public function __set($name, $value) {
		$this->offsetSet($name, $value);
	}

 	/*
		Function: __unset
			Unset a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Void
	*/
	public function __unset($name) {
		$this->offsetUnset($name);
	}

 	/*
		Function: __toString
			Get string (via magic method)

		Returns:
			String
	*/
    public function __toString() {
        return empty($this) ? '' : $this->_write($this->getArrayCopy());
    }

	/*
		Function: _read
			Read array
	*/
	protected function _read($array = array()) {
		return $array;
	}

	/*
		Function: _write
			Serialize array
	*/
	protected function _write($data) {
		return serialize($data);
	}

	/*
		Function: find
			Find a key also in nested arrays/objects

		Parameters:
			$key - Search key (e.g config.database.myvalue)
			$default - Default value, return if key was not found
			$separator - Separator for array/object search key

		Returns:
			Mixed
	*/
	public function find($key, $default = null, $separator = '.') {

		$key   = (string) $key;
		$value = $this->get($key);

		// check if key exists in array
		if ($value !== null) {
			return $value;
		}

		// explode search key and init search data
		$parts = explode($separator, $key);
		$data  = $this;

		foreach ($parts as $part) {

			// handle ArrayObject and Array
			if (($data instanceof ArrayObject || is_array($data)) && isset($data[$part])) {
				$data =& $data[$part];
				continue;
			}

			// handle object
			if (is_object($data) && isset($data->$part)) {
				$data =& $data->$part;
				continue;
			}

			return $default;
		}

		// return existing value
		return $data;
	}

	/*
		Function: searchRecursive
			Find a value also in nested arrays/objects

		Parameters:
			$needle - the value to search for

		Returns:
			Mixed
	*/
	public function searchRecursive($needle) {
		$aIt = new RecursiveArrayIterator($this);
		$it	 = new RecursiveIteratorIterator($aIt);

		while ($it->valid()) {
			if ($it->current() == $needle) {
				return $aIt->key();
			}

			$it->next();
		}

		return false;
	}

	/*
		Function: flattenRecursive
			Return flattened array copy. Keys are NOT preserved.

		Returns:
			array
	*/
	public function flattenRecursive() {
		$flat = array();
		foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($this)) as $value) {
			$flat[] = $value;
		}
		return $flat;
	}

}